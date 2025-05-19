<?php

namespace Totocsa\DatabaseTranslationLocally\database\seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Totocsa\DatabaseTranslationLocally\Models\Locale;
use Totocsa\DatabaseTranslationLocally\database\Traits\InsertsIntoLocales as TraitsInsertsIntoLocales;

class InsertsIntoLocales extends Seeder
{
    use TraitsInsertsIntoLocales;

    public function run(): void
    {
        $rules = Locale::rules();

        DB::beginTransaction();

        $appLocale = env('APP_LOCALE', 'en');
        $allValid = true;
        foreach ($this->localeItems as $k => $v) {
            $attributes = [
                'configname' => $k,
                'enabled' => $k === $appLocale,
                'name' => $v['name'],
                'script' => $v['script'],
                'native' => $v['native'],
                'regional' => $v['regional'],
                'flag' => $this->getFlag($k),
            ];

            $validator = Validator::make($attributes, $rules);
            if ($validator->passes('translatable')) {
                $locale = new Locale();
                $locale->setRawAttributes($attributes);
                $locale->save();
            } else {
                $allValid = false;

                echo "$k\n";
                echo json_encode($validator->errors()) . "\n";
            }
        }

        if ($allValid) {
            DB::commit();
        } else {
            DB::rollBack();
        }
    }
}
