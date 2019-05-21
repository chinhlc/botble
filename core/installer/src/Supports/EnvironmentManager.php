<?php

namespace Botble\Installer\Helpers;

use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;

class EnvironmentManager
{
    /**
     * @var string
     */
    protected $envPath;

    /**
     * @var string
     */
    protected $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function save(Request $request)
    {
        $results = trans('core.installer::installer.environment.success');

        $key = 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')));

        $content = file_get_contents($this->envExamplePath);

        $replacements = [
            'APP_NAME'      => [
                'default' => '"Botble CMS"',
                'value'   => '"' . $request->app_name . '"',
            ],
            'APP_KEY'       => [
                'default' => 'base64:jPDTWLGPka60Lg927hn8Qcxt9ZiuAiXT3XZAf8mwWu4=',
                'value'   => $key,
            ],
            'APP_URL'       => [
                'default' => 'http:\/\/localhost',
                'value'   => $request->app_url,
            ],
            'DB_CONNECTION' => [
                'default' => 'mysql',
                'value'   => $request->database_connection,
            ],
            'DB_HOST'       => [
                'default' => '127.0.0.1',
                'value'   => $request->database_hostname,
            ],
            'DB_PORT'       => [
                'default' => '3306',
                'value'   => $request->database_port,
            ],
            'DB_DATABASE'   => [
                'default' => '',
                'value'   => $request->database_name,
            ],
            'DB_USERNAME'   => [
                'default' => '',
                'value'   => $request->database_username,
            ],
            'DB_PASSWORD'   => [
                'default' => '',
                'value'   => $request->database_password,
            ],
        ];

        foreach ($replacements as $key => $replacement) {
            $content = preg_replace('/^' . $key . '=' . $replacement['default'] . '/m', $key . '=' . $replacement['value'], $content);
        }

        try {
            file_put_contents($this->envPath, $content);
        } catch (Exception $e) {
            $results = trans('core.installer::installer.environment.errors');
        }

        return $results;
    }
}
