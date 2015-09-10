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

namespace Cartalyst\Settings;

use Closure;
use Cartalyst\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * The permission callback.
     *
     * @var \Closure
     */
    protected $permission;

    /**
     * Sets the permission callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function permission(Closure $callback)
    {
        $this->permission = $callback;
    }

    /**
     * Run the permission callback if it does exist on the collection.
     *
     * @return void
     */
    public function beforeCallback()
    {
        foreach ($this->items as $item) {
            $permission = $item->permission;

            if ($permission instanceof Closure && $permission() === false) {
                unset($this->items[$item->id]);
            }
        }
    }
}
