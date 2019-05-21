<?php

namespace Botble\Installer\Http\Middleware;

use Closure;
use Route;

class RedirectIfNotInstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->alreadyInstalled() && Route::current()->getPrefix() !== 'install') {
            return redirect()->route('installers.welcome');
        }

        return $next($request);
    }

    /**
     * If application is already installed.
     *
     * @return bool
     */
    public function alreadyInstalled()
    {
        return file_exists(base_path('.env'));
    }
}
