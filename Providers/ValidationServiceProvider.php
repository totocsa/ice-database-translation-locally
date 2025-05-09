<?php

namespace Totocsa\DatabaseTranslationLocally\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator as LaravelValidator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Totocsa\DatabaseTranslationLocally\Validation\Validator;
use Totocsa\DatabaseTranslationLocally\Validation\Facades\Validator as FacadesValidator;

class ValidationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Itt nincs szükség regisztrálásra, mert a makeWithTemplate metódust csak a boot metódusban használjuk
    }

    public function boot()
    {
        LaravelValidator::extend('makeWithTemplate', [FacadesValidator::class, 'makeWithTemplate']);

        $this->app->extend(ValidatorFactory::class, function ($factory, $app) {
            $factory->resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
                return new Validator($translator, $data, $rules, $messages, $customAttributes);
            });

            return $factory;
        });
    }
}
