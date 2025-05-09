<?php

namespace Totocsa\DatabaseTranslationLocally\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LoadTranslations
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->ajax()) {
            $translations = DB::table('translationoriginals')
                ->select(
                    'translationoriginals.category',
                    'translationoriginals.subtitle as original_subtitle',
                    'locales.configname as locale',
                    'translationvariants.subtitle as translated_subtitle'
                )
                ->join('translationvariants', 'translationoriginals.id', '=', 'translationvariants.translationoriginals_id')
                ->join('locales', 'translationvariants.locales_id', '=', 'locales.id')
                ->get();

            Inertia::share([
                'translations' => $translations,
            ]);
        }

        return $next($request);
    }
}
