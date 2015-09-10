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

namespace Cartalyst\Permissions;

use Closure;
use Cartalyst\Support\Collection;

class Permission extends Collection
{
    /**
     * Sets the controller and the methods for this permission.
     *
     * @param  string  $name
     * @param  string|array  $methods
     * @return void
     */
    public function controller($name, $methods = null)
    {
        $this->controller = $name;

        if (is_string($methods)) {
            $methods = array_map('trim', explode(',', $methods));
        }

        $this->methods = (array) $methods;
    }
}
