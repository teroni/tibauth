<?php

/**
 * Part of the Extensions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Extensions
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Extensions\Laravel;

use Cartalyst\Extensions\Extension;
use Cartalyst\Extensions\FileFinder;
use Cartalyst\Extensions\ExtensionBag;
use Illuminate\Support\ServiceProvider;

class ExtensionsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $app = $this->app;

        // We told a little lie in the configuration. Extensions are actually
        // auto-registered upon booting of the Extensions Service Provider as
        // we had no access to configuration
        if ($app['config']->get('cartalyst.extensions.auto_register')) {
            Extension::setConnectionResolver($app['db']);
            Extension::setEventDispatcher($app['events']);
            Extension::setMigrator($app['migrator']);

            $app['extensions']->findAndRegisterExtensions();
            $app['extensions']->sortExtensions();

            // Now we will check if the extensions should be auto-booted.
            if ($app['config']->get('cartalyst.extensions.auto_boot')) {
                foreach ($app['extensions'] as $extension) {
                    $extension->setupDatabase();
                }

                foreach ($app['extensions']->allEnabled() as $extension) {
                    $extension->boot();
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->prepareResources();

        $this->registerExtensionsFinder();

        $this->registerExtensions();
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        // Publish config
        $config = realpath(__DIR__.'/../config/config.php');

        $this->mergeConfigFrom($config, 'cartalyst.extensions');

        $this->publishes([
            $config => config_path('cartalyst.extensions.php'),
        ], 'config');
    }

    /**
     * Registers the extensions finder.
     *
     * @return void
     */
    protected function registerExtensionsFinder()
    {
        $this->app['extensions.finder'] = $this->app->share(function ($app) {
            $paths = $app['config']->get('cartalyst.extensions.paths');

            return new FileFinder($app['files'], $paths);
        });
    }

    /**
     * Registers the extensions bag.
     *
     * @return void
     */
    protected function registerExtensions()
    {
        $this->app['extensions'] = $this->app->share(function ($app) {
            return new ExtensionBag($app['files'], $app['extensions.finder'], $app, [], $app['cache']);
        });

        $this->app->alias('extensions', 'Cartalyst\Extensions\ExtensionBag');
    }
}
