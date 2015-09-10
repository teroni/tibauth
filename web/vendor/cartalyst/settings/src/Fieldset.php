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

class Fieldset extends Collection
{
    /**
     * Returns a field instance.
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function field($id, Closure $callback = null)
    {
        if ( ! $field = $this->find($id)) {
            $this->put($id, $field = new Field($id));
        }

        $field->executeCallback($callback);

        return $field;
    }

    /**
     * Checks if the Fieldset has any fields registered.
     *
     * @return bool
     */
    public function hasFields()
    {
        return (bool) $this->count();
    }

    /**
     * {@inheritDoc}
     */
    public function attach(BaseCollection $collection, $type = 'Cartalyst\Settings\Field')
    {
        return parent::attach($collection, $type);
    }
}
