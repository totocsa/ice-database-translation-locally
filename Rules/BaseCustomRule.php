<?php

namespace Totocsa\DatabaseTranslationLocally\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Totocsa\DatabaseTranslationLocally\Models\Translationoriginal;

class BaseCustomRule implements Rule
{
    public $message;
    public $messages = [];
    public $parameters = [];

    public function passes($attribute, $value) {}

    public function message() {}

    public function insertIfNotExistsIntoTranslationoriginals()
    {
        foreach ($this->messages as $category => $subtitle) {
            $translationoriginal = Translationoriginal::where('category', $category)->first();
            if (!is_object($translationoriginal)) {
                $translationoriginal = new Translationoriginal([
                    'category' => $category,
                    'subtitle' => $subtitle,
                ]);

                $translationoriginal->save();
            }
        }
    }
}
