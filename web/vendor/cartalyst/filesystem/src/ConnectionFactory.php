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

namespace Cartalyst\Filesystem;

use Cartalyst\Filesystem\FilesystemManager;
use Cartalyst\Filesystem\Adapters\AdapterFactory;

class ConnectionFactory
{
    /**
     * The Adapter Factory instance.
     *
     * @var \Cartalyst\Filesystem\Adapters\AdapterFactory
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param  \Cartalyst\Filesystem\Adapters\AdapterFactory  $adapter
     * @return void
     */
    public function __construct(AdapterFactory $adapter = null)
    {
        $this->adapter = $adapter ?: new AdapterFactory();
    }

    /**
     * Creates a new Filesystem instance.
     *
     * @param  array  $config
     * @param  \Cartalyst\Filesystem\FilesystemManager  $manager
     * @return \Cartalyst\Filesystem\Filesystem
     */
    public function make(array $config, FilesystemManager $manager)
    {
        $adapter = $this->adapter->make($config);

        $filesystem = new Filesystem($adapter);

        $filesystem->setManager($manager);

        return $filesystem;
    }
}
