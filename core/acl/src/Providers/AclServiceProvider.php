<?php

namespace Botble\ACL\Providers;

use Botble\ACL\Facades\AclManagerFacade;
use Botble\ACL\Http\Middleware\Authenticate;
use Botble\ACL\Http\Middleware\RedirectIfAuthenticated;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\RoleUser;
use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Caches\RoleCacheDecorator;
use Botble\ACL\Repositories\Caches\RoleUserCacheDecorator;
use Botble\ACL\Repositories\Eloquent\RoleRepository;
use Botble\ACL\Repositories\Eloquent\RoleUserRepository;
use Botble\ACL\Repositories\Eloquent\UserRepository;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\RoleUserInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Support\Services\Cache\Cache;
use Event;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AclServiceProvider
 * @package Botble\ACL
 */
class AclServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public function register()
    {
        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('auth', Authenticate::class);
        $router->aliasMiddleware('guest', RedirectIfAuthenticated::class);

        $this->app->singleton(UserInterface::class, function () {
            return new UserRepository(new User());
        });

        if (setting('enable_cache', false)) {
            $this->app->singleton(RoleInterface::class, function () {
                return new RoleCacheDecorator(new RoleRepository(new Role()), new Cache($this->app['cache'], __CLASS__));
            });

            $this->app->singleton(RoleUserInterface::class, function () {
                return new RoleUserCacheDecorator(new RoleUserRepository(new RoleUser()), new Cache($this->app['cache'], __CLASS__));
            });
        } else {
            $this->app->singleton(RoleInterface::class, function () {
                return new RoleRepository(new Role());
            });

            $this->app->singleton(RoleUserInterface::class, function () {
                return new RoleUserRepository(new RoleUser());
            });
        }

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->app->register(EventServiceProvider::class);

        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/acl')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishPublicFolder()
            ->publishAssetsFolder()
            ->loadRoutes(['web'])
            ->loadMigrations();

        config()->set(['auth.providers.users.model' => User::class]);

        $this->app->register(FoundationServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('AclManager', AclManagerFacade::class);

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-role-permission',
                    'priority' => 2,
                    'parent_id' => 'cms-core-platform-administration',
                    'name' => trans('core.acl::permissions.role_permission'),
                    'icon' => null,
                    'url' => route('roles.list'),
                    'permissions' => ['roles.list'],
                ])
                ->registerItem([
                    'id' => 'cms-core-user',
                    'priority' => 3,
                    'parent_id' => 'cms-core-platform-administration',
                    'name' => trans('core.acl::permissions.user_management'),
                    'icon' => null,
                    'url' => route('users.list'),
                    'permissions' => ['users.list'],
                ])
                ->registerItem([
                    'id' => 'cms-core-user-super',
                    'priority' => 4,
                    'parent_id' => 'cms-core-platform-administration',
                    'name' => trans('core.acl::permissions.super_user_management'),
                    'icon' => null,
                    'url' => route('users-supers.list'),
                    'permissions' => ['users-supers.list'],
                ]);

            admin_bar()->registerLink('User', route('users.create'), 'add-new');
        });
    }
}
