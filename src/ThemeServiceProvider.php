<?php

namespace Hilabs\Theme;

use Hilabs\Theme\Theme;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/theme.php' => config_path('theme.php')
        ]);
        $this->registerHelpers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/theme.php', 'hilabs.theme'
        );
        $this->registerServices();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['theme'];
    }

    /**
     * Register the helpers file.
     */
    public function registerHelpers()
    {
        require __DIR__.'/helpers.php';
    }

    /**
     * Register the package services.
     *
     * @return void
     */
    protected function registerServices()
    {
        $this->app->singleton('theme', function($app) {
            return new Theme($app['files'], $app['config'], $app['view']);
        });

        $this->app->booting(function($app) {
            $app['theme']->register();
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('Theme', 'Hilabs\Theme\Facades\ThemeFacade');
    }
}
