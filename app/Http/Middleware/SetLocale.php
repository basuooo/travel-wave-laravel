<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (! in_array($locale, ['en', 'ar'], true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        view()->share('currentLocale', $locale);
        view()->share('isRtl', $locale === 'ar');

        return $next($request);
    }
}
