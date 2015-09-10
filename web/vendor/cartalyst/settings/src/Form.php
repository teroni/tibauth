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

class Form extends Collection
{
    /**
     * Returns a section instance.
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function section($id, Closure $callback = null)
    {
        if ( ! $section = $this->find($id)) {
            $this->put($id, $section = new Section($id));
        }

        $section->executeCallback($callback);

        return $section;
    }

    /**
     * Checks if the container has any sections registered.
     *
     * @return bool
     */
    public function hasSections()
    {
        return (bool) $this->count();
    }

    /**
     * {@inheritDoc}
     */
    public function attach(BaseCollection $collection, $type = 'Cartalyst\Settings\Section')
    {
        return parent::attach($collection, $type);
    }
}
