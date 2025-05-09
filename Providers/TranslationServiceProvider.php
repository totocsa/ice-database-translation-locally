<?php

namespace Totocsa\DatabaseTranslationLocally\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
//use Illuminate\Translation\Translator;
use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;
use Totocsa\DatabaseTranslationLocally\Translation\DatabaseLoader;
use Totocsa\DatabaseTranslationLocally\Translation\Translator;

class TranslationServiceProvider extends LaravelTranslationServiceProvider implements DeferrableProvider
{
    /**
     * Register the translation line loader. This method registers a
     * `TranslationLoaderManager` instead of a simple `FileLoader` as the
     * applications `translation.loader` instance.
     */
    protected function registerLoader(): void
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new DatabaseLoader($app['files'], [__DIR__ . '/lang', $app['path.lang']]);
        });

        $this->app->extend('translator', function ($translator, $app) {
            return new Translator(
                $app['translation.loader'],
                $app['config']['app.locale']
            );
        });
    }

    /**
     * @return void
     */
    protected function registerTranslator(): void
    {
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app->getLocale();

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app->getFallbackLocale());

            return $trans;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['translator', 'translation.loader'];
    }
}
