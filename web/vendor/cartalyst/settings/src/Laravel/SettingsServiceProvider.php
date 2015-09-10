<?php

/**
 * Part of the Settings package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Settings
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Settings\Laravel;

use Cartalyst\Settings\Repository;
use Cartalyst\Settings\SectionPrepare;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Settings\SectionValidate;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->registerSectionPrepare();

        $this->registerSectionValidate();

        $this->registerSettingsRepository();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'cartalyst.settings',
            'cartalyst.settings.prepare',
            'cartalyst.settings.validate',
        ];
    }

    /**
     * Register the fieldset prepare instance.
     *
     * @return void
     */
    protected function registerSectionPrepare()
    {
        $this->app['cartalyst.settings.prepare'] = $this->app->share(function ($app) {
            return new SectionPrepare($app['config'], $app['request']);
        });

        $this->app->alias('cartalyst.settings.prepare', 'Cartalyst\Settings\SectionPrepare');
    }

    /**
     * Register the fieldset validation instance.
     *
     * @return void
     */
    protected function registerSectionValidate()
    {
        $this->app['cartalyst.settings.validate'] = $this->app->share(function ($app) {
            return new SectionValidate($app['validator']);
        });

        $this->app->alias('cartalyst.settings.validate', 'Cartalyst\Settings\SectionValidate');
    }

    /**
     * Register the settings repository.
     *
     * @return void
     */
    protected function registerSettingsRepository()
    {
        $this->app['cartalyst.settings'] = $this->app->share(function () {
            return new Repository('cartalyst');
        });

        $this->app->alias('cartalyst.settings', 'Cartalyst\Settings\Repository');
    }
}
