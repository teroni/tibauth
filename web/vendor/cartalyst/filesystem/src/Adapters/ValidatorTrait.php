<?php

/**
 * Part of the Filesystem package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Filesystem
 * @version    3.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Filesystem\Adapters;

trait ValidatorTrait
{
    /**
     * Validates required parameters.
     *
     * @param  array  $config
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validate($config)
    {
        foreach ($this->required as $key) {
            if (! array_get($config, $key)) {
                throw new \InvalidArgumentException("$key is required.");
            }
        }
    }
}
