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

class Section extends Collection
{
    /**
     * Returns a fieldset instance.
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function fieldset($id, Closure $callback = null)
    {
        if ( ! $fieldset = $this->find($id)) {
            $this->put($id, $fieldset = new Fieldset($id));
        }

        $fieldset->executeCallback($callback);

        return $fieldset;
    }

    /**
     * Checks if the section has any fieldsets registered.
     *
     * @return bool
     */
    public function hasFieldsets()
    {
        return (bool) $this->count();
    }

    /**
     * Checks if any of the section fieldsets has any registered fields.
     *
     * @return bool
     */
    public function anyFieldsetHasFields()
    {
        $found = false;

        foreach ($this->all() as $fieldset) {
            if ($fieldset->hasFields()) {
                $found = true;

                break;
            }
        }

        return $found;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(BaseCollection $collection, $type = 'Cartalyst\Settings\Fieldset')
    {
        return parent::attach($collection, $type);
    }
}
