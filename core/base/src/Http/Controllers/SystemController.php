<?php

namespace Botble\Base\Http\Controllers;

use App\Console\Kernel;
use Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\SystemManagement;
use Botble\Base\Tables\InfoTable;
use Botble\Table\TableBuilder;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SystemController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getInfo(Request $request, TableBuilder $tableBuilder)
    {
        page_title()->setTitle(trans('core.base::system.info.title'));

        Assets::addAppModule(['system-info'])
            ->addStylesheetsDirectly(['vendor/core/css/system-info.css']);

        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        $infoTable = $tableBuilder->create(InfoTable::class);

        if ($request->expectsJson()) {
            return $infoTable->renderTable();
        }

        $systemEnv = SystemManagement::getSystemEnv();
        $serverEnv = SystemManagement::getServerEnv();
        $serverExtras = SystemManagement::getServerExtras();
        $systemExtras = SystemManagement::getSystemExtras();
        $extraStats = SystemManagement::getExtraStats();
        return view('core.base::system.info', compact(
            'packages',
            'infoTable',
            'systemEnv',
            'serverEnv',
            'extraStats',
            'serverExtras',
            'systemExtras'
        ));
    }

    /**
     * Show all plugins in system
     *
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getListPlugins()
    {
        page_title()->setTitle(trans('core.base::system.plugins'));

        if (File::exists(base_path('plugins/.DS_Store'))) {
            File::delete(base_path('plugins/.DS_Store'));
        }
        $plugins = scan_folder(base_path('plugins'));
        foreach ($plugins as $plugin) {
            if (File::exists(base_path('plugins/' . $plugin . '/.DS_Store'))) {
                File::delete(base_path('plugins/' . $plugin . '/.DS_Store'));
            }
        }

        Assets::addAppModule(['plugin']);

        $plugins = scan_folder(config('core.base.general.plugin_path'));
        if (!empty($plugins)) {
            $installed = get_active_plugins();
            foreach ($plugins as $plugin) {
                $plugin_path = config('core.base.general.plugin_path') . DIRECTORY_SEPARATOR . $plugin;
                $content = get_file_data($plugin_path . '/plugin.json');
                if (!empty($content)) {
                    if (!in_array($plugin, $installed)) {
                        $content['status'] = 0;
                    } else {
                        $content['status'] = 1;
                    }

                    $content['path'] = $plugin;
                    $content['image'] = null;
                    if (File::exists($plugin_path . '/screenshot.png')) {
                        $content['image'] = base64_encode(File::get($plugin_path . '/screenshot.png'));
                    }
                    $list[] = (object)$content;
                }
            }
        }
        return view('core.base::plugins.list', compact('list'));
    }

    /**
     * Activate or Deactivate plugin
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param Kernel $kernel
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author Sang Nguyen
     */
    public function getChangePluginStatus(Request $request, BaseHttpResponse $response, Kernel $kernel)
    {
        $plugin = strtolower($request->input('alias'));

        $content = get_file_data(config('core.base.general.plugin_path') .
            DIRECTORY_SEPARATOR . $plugin . '/plugin.json');
        if (!empty($content)) {

            try {
                $activated_plugins = get_active_plugins();
                if (!in_array($plugin, $activated_plugins)) {
                    if (!empty(array_get($content, 'require'))) {
                        $count_required_plugins = count(array_intersect($content['require'], $activated_plugins));
                        $valid = $count_required_plugins == count($content['require']);
                        if (!$valid) {
                            return $response
                                ->setError()
                                ->setMessage(trans('core.base::system.missing_required_plugins', [
                                    'plugins' => implode(',', $content['require'])
                                ]));
                        }
                    }

                    $kernel->call('cms:plugin:activate', ['name' => $plugin]);
                } else {
                    $kernel->call('cms:plugin:deactivate', ['name' => $plugin]);
                }

                return $response->setMessage(trans('core.base::system.update_plugin_status_success'));
            } catch (Exception $ex) {
                info($ex->getMessage());
                return $response
                    ->setError()
                    ->setMessage($ex->getMessage());
            }
        }
        return $response
            ->setError()
            ->setMessage(trans('core.base::system.invalid_plugin'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     */
    public function getCacheManagement()
    {
        page_title()->setTitle(trans('core.base::cache.cache_management'));

        Assets::addAppModule(['cache']);
        return view('core.base::system.cache');
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param Kernel $kernel
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postClearCache(Request $request, BaseHttpResponse $response, Kernel $kernel)
    {
        switch ($request->input('type')) {
            case 'clear_cms_cache':
                $kernel->call('cache:clear');
                break;
            case 'refresh_compiled_views':
                $kernel->call('view:clear');
                break;
            case 'clear_config_cache':
                $kernel->call('config:clear');
                break;
            case 'clear_route_cache':
                $kernel->call('route:clear');
                break;
            case 'clear_log':
                $kernel->call('cms:log:clear');
                break;
        }

        return $response->setMessage(trans('core.base::cache.commands.' . $request->input('type') . '.success_msg'));
    }

    /**
     * Remove plugin
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param Kernel $kernel
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author Sang Nguyen
     */
    public function postRemovePlugin(Request $request, BaseHttpResponse $response, Kernel $kernel)
    {
        $plugin = strtolower($request->input('plugin'));

        if (in_array($plugin, scan_folder(base_path('plugins')))) {
            try {
                $kernel->call('cms:plugin:remove', ['name' => $plugin, '--force' => true]);
                return $response->setMessage(trans('core.base::system.remove_plugin_success'));
            } catch (Exception $ex) {
                info($ex->getMessage());
                return $response
                    ->setError()
                    ->setMessage($ex->getMessage());
            }
        }
        return $response
            ->setError()
            ->setMessage(trans('core.base::system.invalid_plugin'));
    }
}
