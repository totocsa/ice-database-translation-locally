<?php

namespace Totocsa\DatabaseTranslationLocally\Rules;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Totocsa\DatabaseTranslationLocally\Rules\BaseCustomRule;

class MustEnabledTrue extends BaseCustomRule
{
    public $message;
    public $messages = [
        'mustenabledtrue.one' => 'The :attribute field must be true when configname is :defaultLocale.',
        'mustenabledtrue.many' => 'The :attribute field must be true when configname is :defaultLocale or :currentLocale.',
    ];

    public $defaultLocale;
    public $currentLocale;

    public function __construct()
    {
        $this->defaultLocale = LaravelLocalization::getDefaultLocale();
        $this->currentLocale = LaravelLocalization::getCurrentLocale();

        $this->parameters = [':defaultLocale' => $this->defaultLocale];
        if ($this->defaultLocale === $this->currentLocale) {
            $this->message = $this->messages['mustenabledtrue.one'];
        } else {
            $this->message = $this->messages['mustenabledtrue.many'];
            $this->parameters[':currentLocale'] = $this->currentLocale;
        }

        $this->insertIfNotExistsIntoTranslationoriginals();
    }

    public function passes($attribute, $value, $attributes = [])
    {
        if (in_array($attributes['configname'], [$this->defaultLocale, $this->currentLocale])) {
            return $value;
        } else {
            return true;
        }
    }

    public function message()
    {
        $index = $this->defaultLocale === $this->currentLocale ? 'one' : 'many';
        return "mustenabledtrue.$index";
    }

    /*protected function insertIfNotExistsIntoTranslationoriginals()
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
    }*/
}
