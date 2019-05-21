<?php

namespace Botble\Member\Providers;

use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Member\Http\Middleware\RedirectIfMember;
use Botble\Member\Http\Middleware\RedirectIfNotMember;
use Botble\Member\Models\Member;
use Botble\Member\Repositories\Caches\MemberCacheDecorator;
use Botble\Member\Repositories\Eloquent\MemberRepository;
use Botble\Member\Repositories\Interfaces\MemberInterface;
use Botble\Support\Services\Cache\Cache;
use Event;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MemberServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @author Sang Nguyen
     */
    public function register()
    {
        config([
            'auth.guards.member' => [
                'driver' => 'session',
                'provider' => 'members',
            ],
            'auth.providers.members' => [
                'driver' => 'eloquent',
                'model' => Member::class,
            ],
            'auth.passwords.members' => [
                'provider' => 'members',
                'table' => 'member_password_resets',
                'expire' => 60,
            ],
        ]);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('member', RedirectIfNotMember::class);
        $router->aliasMiddleware('member.guest', RedirectIfMember::class);

        if (setting('enable_cache', false)) {
            $this->app->singleton(MemberInterface::class, function () {
                return new MemberCacheDecorator(new MemberRepository(new Member()), new Cache($this->app['cache'], MemberRepository::class));
            });
        } else {
            $this->app->singleton(MemberInterface::class, function () {
                return new MemberRepository(new Member());
            });
        }

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('plugins/member')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssetsFolder()
            ->publishPublicFolder();

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-core-member',
                'priority' => 22,
                'parent_id' => null,
                'name' => trans('plugins.member::member.menu_name'),
                'icon' => 'fa fa-users',
                'url' => route('member.list'),
                'permissions' => ['member.list'],
            ]);
        });
    }
}
