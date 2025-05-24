<?php

namespace Totocsa\DatabaseTranslationLocally;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Totocsa\MigrationHelper\MigrationHelper;

class DatabaseTranslationLocallyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Ha van konfigurációs fájl, azt itt töltheted be
        //$this->mergeConfigFrom(__DIR__.'/../config/destroy-confirm-modal.php', 'destroy-confirm-modal');
    }

    public function boot($r = null)
    {
        $groupsBase = 'ice-database-translation-locally';
        $groups = "$groupsBase-migrations";

        $paths = MigrationHelper::stubsToMigrations($groups, __DIR__ . '/database/migrations/');

        $this->publishes($paths, $groups);
        $this->publishes([__DIR__ . '/resources/js' =>  resource_path("js/totocsa/$groupsBase")], "$groupsBase-resources");
    }
}
