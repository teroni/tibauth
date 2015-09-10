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

class Repository extends BaseCollection
{
    /**
     * Returns a form instance.
     *
     * @param  mixed  $id
     * @param  \Closure  $callback
     * @return \Illuminate\Support\Collection
     */
    public function form($id, Closure $callback = null)
    {
        if ( ! $form = $this->find($id)) {
            $this->put($id, $form = new Form($id));
        }

        $form->executeCallback($callback);

        return $form;
    }

    /**
     * Checks if the repository has any forms registered.
     *
     * @return bool
     */
    public function hasForms()
    {
        return (bool) $this->count();
    }

    /**
     * {@inheritDoc}
     */
    public function attach(BaseCollection $collection, $type = 'Cartalyst\Settings\form')
    {
        return parent::attach($collection, $type);
    }
}
