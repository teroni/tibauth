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

class Group extends Collection
{
    /**
     * Returns a permission instance.
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function permission($id, Closure $callback = null)
    {
        if ( ! $permission = $this->find($id)) {
            $this->put($id, $permission = new Permission($id));
        }

        $permission->executeCallback($callback);

        return $permission;
    }

    /**
     * Checks if the group has any permissions registered.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return (bool) $this->count();
    }
}
