<?php

namespace Botble\ACL\Providers;

use Botble\ACL\AclManager;
use Botble\ACL\Activations\IlluminateActivationRepository;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\ACL\Roles\IlluminateRoleRepository;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->garbageCollect();
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->setOverrides();
        $this->registerRoles();
        $this->registerAcl();
        $this->registerActivations();
    }

    /**
     * Registers the roles.
     *
     * @return void
     */
    protected function registerRoles()
    {
        $this->app->singleton('acl.roles', function (Application $app) {
            $config = $app['config']->get('core.acl.general.roles');

            return new IlluminateRoleRepository($config['model']);
        });
    }

    /**
     * Registers the activations.
     *
     * @return void
     */
    protected function registerActivations()
    {
        $this->app->singleton('acl.activations', function (Application $app) {
            $config = $app['config']->get('core.acl.general.activations');

            return new IlluminateActivationRepository($config['model'], $config['expires']);
        });
    }

    /**
     * Registers acl.
     *
     * @return void
     */
    protected function registerAcl()
    {
        $this->app->singleton('AclManagerFacade', function (Application $app) {
            $acl = new AclManager(
                $app[UserInterface::class],
                $app['acl.roles'],
                $app['acl.activations']
            );

            $acl->setActivationRepository($app['acl.activations']);

            return $acl;
        });

        $this->app->alias('acl', AclManager::class);
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'acl.roles',
            'acl.activations',
            'AclManagerFacade',
        ];
    }

    /**
     * Garbage collect activations and reminders.
     *
     * @return void
     */
    protected function garbageCollect()
    {
        $config = $this->app['config']->get('core.acl.general');

        $this->sweep($this->app['acl.activations'], $config['activations']['lottery']);
    }

    /**
     * Sweep expired codes.
     *
     * @param  mixed $repository
     * @param  array $lottery
     * @return void
     */
    protected function sweep($repository, array $lottery)
    {
        if ($this->configHitsLottery($lottery)) {
            try {
                $repository->removeExpired();
            } catch (Exception $exception) {
                info($exception->getMessage());
            }
        }
    }

    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array $lottery
     * @return bool
     */
    protected function configHitsLottery(array $lottery)
    {
        return mt_rand(1, $lottery[1]) <= $lottery[0];
    }

    /**
     * Performs the necessary overrides.
     *
     * @return void
     */
    protected function setOverrides()
    {
        $config = $this->app['config']->get('core.acl.general');

        $users = $config['users']['model'];

        $roles = $config['roles']['model'];

        if (class_exists($users)) {
            if (method_exists($users, 'setRolesModel')) {
                forward_static_call_array([$users, 'setRolesModel'], [$roles]);
            }

            if (method_exists($users, 'setPermissionsClass')) {
                forward_static_call_array([$users, 'setPermissionsClass'], [$config['permissions']['class']]);
            }
        }

        if (class_exists($roles) && method_exists($roles, 'setUsersModel')) {
            forward_static_call_array([$roles, 'setUsersModel'], [$users]);
        }
    }
}
