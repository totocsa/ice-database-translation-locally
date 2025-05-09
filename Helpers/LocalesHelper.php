<?php

namespace Totocsa\DatabaseTranslationLocally\Helpers;

use Illuminate\Support\Facades\DB;

class LocalesHelper
{
    public static function refreshLocalesByConfig()
    {
        $config = config('laravellocalization');
        $supporteds = array_keys($config['supportedLocales']);
        $supportedsJson = json_encode($supporteds);

        $query = "DO $$"
            . " BEGIN"
            . " IF EXISTS (SELECT 1 FROM pg_proc WHERE proname = 'refresh_locales_by_config') THEN"
            . " PERFORM refresh_locales_by_config('{$supportedsJson}');"
            . " END IF;"
            . " END $$;";

        //DB::statement("select refresh_locales_by_config('{$supportedsJson}')");
        DB::statement($query);
    }
}
