<?php

namespace Totocsa\DatabaseTranslationLocally\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = [
            'current' =>   LaravelLocalization::getCurrentLocale(), // Aktuális nyelv
            'supported' =>  LaravelLocalization::getSupportedLocales(), // Elérhető nyelvek
            'flags' => [
                'hu' => 'hu',
                'en' => 'gb',
            ],
        ];

        // Az aktuális nyelv hozzáadása a globális view/share változókhoz
        app()->setLocale($locale['current']);
        view()->share('locale', $locale);

        Inertia::share(['locale' => $locale]);

        return $next($request);
    }
}
