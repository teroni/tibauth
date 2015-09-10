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

class Field extends Collection
{
    /**
     * Holds a list of all the supported input types.
     *
     * @var array
     */
    protected $validTypes = [
        'text',
        'textarea',
    ];

    /**
     * Holds a list of all the supported option input types.
     *
     * @var array
     */
    protected $validOptionTypes = [
        'radio',
        'select',
        'checkbox',
        'multiselect',
    ];

    /**
     * Mutator for the "type" attribute.
     *
     * @param  string  $type
     * @return string
     */
    public function typeAttribute($type)
    {
        if ($this->hasOptions() && ! in_array($type, $this->validOptionTypes)) {
            return 'select';
        }

        return $this->isValidType($type) ? $type : 'text';
    }

    /**
     * Returns an Option instance
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function option($id, Closure $callback = null)
    {
        if ( ! $option = $this->find($id)) {
            $this->put($id, $option = new Option($id));
        }

        $option->executeCallback($callback);

        return $option;
    }

    /**
     * Checks if the field has any options registered.
     *
     * @return bool
     */
    public function hasOptions()
    {
        return (bool) $this->count();
    }

    /**
     * Checks if the given type is valid.
     *
     * @param  string  $type
     * @return bool
     */
    protected function isValidType($type)
    {
        $types = array_merge($this->validTypes, $this->validOptionTypes);

        return in_array($type, $types);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(BaseCollection $collection, $type = 'Cartalyst\Settings\Option')
    {
        return parent::attach($collection, $type);
    }
}
