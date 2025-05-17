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

        $allValid = true;
        foreach ($this->items as $k => $v) {
            $attributes = [
                'configname' => $k,
                'enabled' => $k === 'en',
                'name' => $v['name'],
                'script' => $v['script'],
                'native' => $v['native'],
                'regional' => $v['regional'],
                'flag' => $v['flag'],
            ];

            $validator = Validator::make($attributes, $rules);
            if ($validator->passes()) {
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
