<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

/*
|--------------------------------------------------------------------------
| Model Overrides
|--------------------------------------------------------------------------
|
| The Platform extensions uses the IoC to resolve model instances, you
| can override these here by simply returning your own model which
| should always extend the model you want to override.
|
*/
$this->app['Platform\Users\Models\User'] = new Tib\Models\User;
$this->app['Platform\Roles\Models\Role'] = new Tib\Models\Role;
Sentinel::setModel('Tib\Models\User');