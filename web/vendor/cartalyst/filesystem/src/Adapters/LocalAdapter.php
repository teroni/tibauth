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

class LocalAdapter implements AdapterInterface
{
    use ValidatorTrait;

    /**
     * Required fields.
     *
     * @var array
     */
    protected $required = [
        'path',
    ];

    /**
     * {@inheritDoc}
     */
    public function connect(array $config)
    {
        $this->validate($config);

        $path = array_get($config, 'path');

        return new Local($path);
    }
}
