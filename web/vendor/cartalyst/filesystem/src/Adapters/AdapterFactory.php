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

use InvalidArgumentException;

class AdapterFactory
{
    /**
     * Creates an adapter connection.
     *
     * @param  array  $config
     * @return \Cartalyst\Filesystem\Adapters\AdapterInterface
     */
    public function make(array $config)
    {
        if (! $adapter = array_get($config, 'adapter')) {
            throw new InvalidArgumentException('An adapter must be specified.');
        }

        $className = str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $adapter)));

        $class = 'Cartalyst\\Filesystem\\Adapters\\'."{$className}Adapter";

        if (! class_exists($class)) {
            throw new InvalidArgumentException("Unknown [{$adapter}] adapter!");
        }

        return (new $class($config))->connect($config);
    }
}
