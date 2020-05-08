<?php

namespace Botble\Backup\Supports;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Botble\Base\Supports\PclZip as Zip;
use Illuminate\Filesystem\Filesystem;

class Backup
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * @var string
     */
    protected $folder;

    /**
     * Backup constructor.
     * @param Filesystem $file
     */
    public function __construct(Filesystem $file)
    {
        $this->file = $file;
    }

    /**
     * @param Request $request
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createBackupFolder(Request $request)
    {
        $backupFolder = $this->createFolder(storage_path('app/backup'));
        $now = now(config('app.timezone'))->format('Y-m-d-H-i-s');
        $this->folder = $this->createFolder($backupFolder . DIRECTORY_SEPARATOR . $now);

        $file = storage_path('app/backup/backup.json');
        $data = [];

        if (file_exists($file)) {
            $data = get_file_data($file);
        }

        $data[$now] = [
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'date'        => now(config('app.timezone'))->toDateTimeString(),
        ];
        save_file_data($file, $data);

        return [
            'key'  => $now,
            'data' => $data[$now],
        ];
    }

    /**
     * @param string $folder
     * @return string
     */
    public function createFolder($folder)
    {
        if (!$this->file->isDirectory($folder)) {
            $this->file->makeDirectory($folder);
            chmod($folder, 0777);
        }

        return $folder;
    }

    /**
     * @return array|bool|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getBackupList()
    {
        $file = storage_path('app/backup/backup.json');
        if (file_exists($file)) {
            return get_file_data($file);
        }

        return [];
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function backupDb()
    {
        $file = 'database-' . now(config('app.timezone'))->format('Y-m-d-H-i-s');
        $path = $this->folder . DIRECTORY_SEPARATOR . $file;

        $mysqlPath = rtrim(setting('backup_mysql_execute_path',
            config('plugins.backup.general.backup_mysql_execute_path')), '/');

        if (!empty($mysqlPath)) {
            $mysqlPath = $mysqlPath . '/';
        }

        $sql = $mysqlPath . 'mysqldump --user=' . config('database.connections.mysql.username') . ' --password=' .
            config('database.connections.mysql.password');

        if (!in_array(config('database.connections.mysql.host'), ['localhost', '127.0.0.1'])) {
            $sql .= ' --host=' . config('database.connections.mysql.host');
        }

        $sql .= ' --port=' . config('database.connections.mysql.port') . ' ' . config('database.connections.mysql.database') . ' > ' . $path . '.sql';

        system($sql);
        $this->compressFileToZip($path, $file);
        if (file_exists($path . '.zip')) {
            chmod($path . '.zip', 0777);
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $name
     * @throws Exception
     */
    public function compressFileToZip($path, $name)
    {
        $filename = $path . '.zip';

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive;
            if ($zip->open($filename, ZipArchive::CREATE) == true) {
                $zip->addFile($path . '.sql', $name . '.sql');
                $zip->close();
            }
        } else {
            $archive = new Zip($filename);
            $archive->add($path . '.sql', PCLZIP_OPT_REMOVE_PATH, $filename);
        }
        $this->deleteFile($path . '.sql');
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function deleteFile($file)
    {
        if ($this->file->exists($file)) {
            $this->file->delete($file);
        }
    }

    /**
     * @param string $source
     * @return bool
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function backupFolder($source)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . 'storage-' . now(config('app.timezone'))->format('Y-m-d-H-i-s') . '.zip';

        ini_set('max_execution_time', 5000);

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive;
            if ($zip->open($file, ZipArchive::CREATE) !== true) {
                $this->deleteFolderBackup($this->folder);
            }
        } else {
            $zip = new Zip($file);
        }
        $arr_src = explode(DIRECTORY_SEPARATOR, $source);
        $path_length = strlen(implode(DIRECTORY_SEPARATOR, $arr_src) . DIRECTORY_SEPARATOR);
        // add each file in the file list to the archive
        $this->recurseZip($source, $zip, $path_length);
        if (class_exists('ZipArchive', false)) {
            $zip->close();
        }
        if (file_exists($file)) {
            chmod($file, 0777);
        }

        return true;
    }

    /**
     * @param string $path
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function deleteFolderBackup($path)
    {
        if ($this->file->isDirectory(storage_path('app/backup')) && $this->file->isDirectory($path)) {
            foreach (scan_folder($path) as $item) {
                $this->file->delete($path . DIRECTORY_SEPARATOR . $item);
            }
            $this->file->deleteDirectory($path);

            if (empty($this->file->directories(storage_path('app/backup')))) {
                $this->file->deleteDirectory(storage_path('app/backup'));
            }
        }

        $file = storage_path('app/backup/backup.json');
        $data = [];
        if (file_exists($file)) {
            $data = get_file_data($file);
        }
        if (!empty($data)) {
            unset($data[Arr::last(explode('/', $path))]);
            save_file_data($file, $data);
        }
    }

    /**
     * @param string $src
     * @param ZipArchive $zip
     * @param string $pathLength
     */
    public function recurseZip($src, &$zip, $pathLength)
    {
        foreach (scan_folder($src) as $file) {
            if ($this->file->isDirectory($src . DIRECTORY_SEPARATOR . $file)) {
                $this->recurseZip($src . DIRECTORY_SEPARATOR . $file, $zip, $pathLength);
            } else {
                if (class_exists('ZipArchive', false)) {
                    /**
                     * @var ZipArchive $zip
                     */
                    $zip->addFile($src . DIRECTORY_SEPARATOR . $file,
                        substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                } else {
                    /**
                     * @var Zip $zip
                     */
                    $zip->add($src . DIRECTORY_SEPARATOR . $file, PCLZIP_OPT_REMOVE_PATH,
                        substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                }
            }
        }
    }

    /**
     * @param string $path
     * @param string $file
     * @return bool
     *
     * @throws Exception
     */
    public function restoreDb($file, $path)
    {
        $this->restore($file, $path);
        $file = $path . DIRECTORY_SEPARATOR . $this->file->name($file) . '.sql';

        if (!file_exists($file)) {
            return false;
        }
        // Force the new login to be used
        DB::purge();
        DB::unprepared('USE `' . config('database.connections.mysql.database') . '`');
        DB::connection()->setDatabaseName(config('database.connections.mysql.database'));
        DB::unprepared(file_get_contents($file));

        $this->deleteFile($file);

        return true;
    }

    /**
     * @param string $fileName
     * @param string $pathTo
     * @return bool
     *
     */
    public function restore($fileName, $pathTo)
    {
        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive;
            if ($zip->open($fileName) === true) {
                $zip->extractTo($pathTo);
                $zip->close();
                return true;
            }
        } else {
            $archive = new Zip($fileName);
            $archive->extract(PCLZIP_OPT_PATH, $pathTo, PCLZIP_OPT_REMOVE_ALL_PATH);
            return true;
        }

        return false;
    }
}
