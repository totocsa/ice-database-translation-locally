<?php

namespace Totocsa\DatabaseTranslationLocally\Validation;

use Illuminate\Validation\Factory;
use Totocsa\DatabaseTranslationLocally\Validation\Facades\Validator;
use Illuminate\Contracts\Translation\Translator;

class ValidatorFactory extends Factory
{
    use FormatsMessages;

    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        return new Validator(
            $this->translator,
            $data,
            $rules,
            $messages,
            $customAttributes
        );
    }
}
