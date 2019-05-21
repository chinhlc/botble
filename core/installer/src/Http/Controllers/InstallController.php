<?php

namespace Botble\Installer\Http\Controllers;

use Artisan;
use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Installer\Events\EnvironmentSaved;
use Botble\Installer\Events\InstallerFinished;
use Botble\Installer\Helpers\DatabaseManager;
use Botble\Installer\Helpers\EnvironmentManager;
use Botble\Installer\Helpers\FinalInstallManager;
use Botble\Installer\Helpers\PermissionsChecker;
use Botble\Installer\Helpers\RequirementsChecker;
use Botble\Installer\Http\Requests\SaveAccountRequest;
use Botble\Installer\Http\Requests\SaveEnvironmentRequest;
use Botble\Setting\Supports\SettingStore;
use DB;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;

class InstallController extends Controller
{
    /**
     * @var RequirementsChecker
     */
    protected $requirements;

    /**
     * @var PermissionsChecker
     */
    protected $permissions;

    /**
     * @var EnvironmentManager
     */
    protected $environmentManager;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @param RequirementsChecker $requirementsChecker
     * @param PermissionsChecker $permissionsChecker
     * @param EnvironmentManager $environmentManager
     * @param DatabaseManager $databaseManager
     * @param UserInterface $userRepository
     */
    public function __construct(
        RequirementsChecker $requirementsChecker,
        PermissionsChecker $permissionsChecker,
        EnvironmentManager $environmentManager,
        DatabaseManager $databaseManager,
        UserInterface $userRepository
    )
    {
        $this->requirements = $requirementsChecker;
        $this->permissions = $permissionsChecker;
        $this->environmentManager = $environmentManager;
        $this->databaseManager = $databaseManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Display the installer welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWelcome()
    {
        return view('core.installer::welcome');
    }

    /**
     * Display the requirements page.
     *
     * @return \Illuminate\View\View
     */
    public function getRequirements()
    {
        $phpSupportInfo = $this->requirements->checkPHPversion(config('core.installer.installer.core.php_version'));
        $requirements = $this->requirements->check(config('core.installer.installer.requirements'));

        return view('core.installer::.requirements', compact('requirements', 'phpSupportInfo'));
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function getEnvironment()
    {
        return view('core.installer::environment');
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param SaveEnvironmentRequest $request
     * @param SettingStore $settingStore
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSaveEnvironment(SaveEnvironmentRequest $request)
    {
        $connectionName = 'database.connections.' . $request->input('database_connection');

        config([
            'database.default' => $request->input('database_connection'),
            $connectionName    => array_merge(config($connectionName), [
                'host'     => $request->input('database_hostname'),
                'port'     => $request->input('database_port'),
                'database' => $request->input('database_name'),
                'username' => $request->input('database_username'),
                'password' => $request->input('database_password'),
            ]),
        ]);

        DB::purge($request->input('database_connection'));

        if (!check_database_connection()) {
            $errors = new MessageBag();
            $errors->add('database_name', __('Wrong database config'));

            return view('core.installer::environment', compact('errors'));
        }

        $results = $this->environmentManager->save($request);

        $this->databaseManager->migrateAndSeed();

        event(new EnvironmentSaved($request));

        return redirect()
            ->route('installers.create_account')
            ->with('install_message', $results);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreateAccount()
    {
        return view('core.installer::account');
    }

    /**
     * @param SaveAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSaveAccount(SaveAccountRequest $request)
    {
        $request->merge([
            'super_user' => 1,
            'manage_supers' => 1,
            'profile_image' => config('core.acl.general.avatar.default'),
            'password' => bcrypt($request->input('password')),
        ]);

        try {
            /**
             * @var User $user
             */
            $user = $this->userRepository->createOrUpdate($request->input());
            if (!empty($user) && acl_activate_user($user)) {
                info('Super user is created.');
            }

            if ($request->input('install_sample_data')) {
                Artisan::call('cms:theme:install-sample-data');
            }
        } catch (Exception $exception) {
            info('User could not be created: ' . $exception->getMessage());
        }

        return redirect()
            ->route('installers.final');
    }

    /**
     * Update installed file and display finished view.
     *
     * @param \Botble\Installer\Helpers\FinalInstallManager $finalInstall
     * @param \Botble\Installer\Helpers\EnvironmentManager $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFinish(FinalInstallManager $finalInstall)
    {
        $finalInstall->runFinal();

        event(new InstallerFinished());

        return view('core.installer::finished');
    }
}