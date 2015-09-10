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

class Option extends BaseCollection
{
    /**
     * Mutator for the "value" attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function valueAttribute($value)
    {
        if (is_bool($value)) {
            $value = json_encode((int) $value);
        }

        return $value;
    }
}
