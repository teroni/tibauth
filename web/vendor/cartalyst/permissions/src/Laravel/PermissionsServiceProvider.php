<?php

/**
 * Part of the Permissions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Permissions
 * @version    1.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Permissions\Laravel;

use Cartalyst\Permissions\Container;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->registerPermissionsContainer();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'cartalyst.permissions',
        ];
    }

    /**
     * Register the permissions container.
     *
     * @return void
     */
    protected function registerPermissionsContainer()
    {
        $this->app['cartalyst.permissions'] = $this->app->share(function () {
            return new Container('cartalyst');
        });
    }
}
