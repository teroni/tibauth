<?php

/**
 * Part of the Filesystem package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Filesystem
 * @version    3.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Filesystem\Laravel;

use Illuminate\Support\ServiceProvider;
use Cartalyst\Filesystem\ConnectionFactory;
use Cartalyst\Filesystem\FilesystemManager;
use Cartalyst\Filesystem\Adapters\AdapterFactory;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->prepareResources();

        $this->registerFilesystem();
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

        $this->mergeConfigFrom($config, 'cartalyst.filesystem');

        $this->publishes([
            $config => config_path('cartalyst.filesystem.php'),
        ], 'config');
    }

    /**
     * Register the Filesystem class.
     *
     * @return void
     */
    protected function registerFilesystem()
    {
        $this->app['cartalyst.filesystem'] = $this->app->share(function ($app) {
            $config = $app['config']->get('cartalyst.filesystem');

            return (new FilesystemManager($config))
                ->setDispersion($config['dispersion'])
                ->setMaxFileSize($config['max_filesize'])
                ->setAllowedMimes($config['allowed_mimes'])
                ->setPlaceholders($config['placeholders']);
        });
    }
}
