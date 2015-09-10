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

use InvalidArgumentException;
use Cartalyst\Filesystem\ConnectionFactory;

class FilesystemManager
{
    /**
     * The Config repository.
     *
     * @var array
     */
    protected $config;

    /**
     * The Connection factory.
     *
     * @var \Cartalyst\Filesystem\ConnectionFactory
     */
    protected $connection;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * Holds the max file size limit
     *
     * @var int
     */
    protected $maxFileSize = 10485760;

    /**
     * Holds all the allowed mime types.
     *
     * @var array
     */
    protected $allowedMimes = [];

    /**
     * Holds all the available placeholders.
     *
     * @var array
     */
    protected $placeholders = [];

    /**
     * Holds the file dispersion.
     *
     * @var string
     */
    protected $dispersion;

    /**
     * Constructor.
     *
     * @param  array  $config
     * @param  \Cartalyst\Filesystem\ConnectionFactory  $connection
     * @return void
     */
    public function __construct(array $config, ConnectionFactory $connection = null)
    {
        $this->config = $config;

        $this->connection = $connection ?: new ConnectionFactory;
    }

    /**
     * Returns a connection instance.
     *
     * @param  string  $name
     * @return \Cartalyst\Filesystem\Filesystem
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (! $connection = array_get($this->connections, $name)) {
            $config = $this->getConnectionConfig($name);

            $connection = $this->connection->make($config, $this);

            array_set($this->connections, $name, $connection);
        }

        return $connection;
    }

    /**
     * Returns the max file size limit.
     *
     * @return int
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Sets the max file size limit.
     *
     * @param  int  $size
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function setMaxFileSize($size)
    {
        $this->maxFileSize = $size;

        return $this;
    }

    /**
     * Returns the allowed mime types.
     *
     * @return array
     */
    public function getAllowedMimes()
    {
        return $this->allowedMimes;
    }

    /**
     * Sets the allowed mime types.
     *
     * @param  array  $mimes
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function setAllowedMimes(array $mimes)
    {
        $this->allowedMimes = $mimes;

        return $this;
    }

    /**
     * Returns the placeholders.
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Sets the placeholders.
     *
     * @param  array  $placeholders
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function setPlaceholders(array $placeholders)
    {
        $this->placeholders = $placeholders;

        return $this;
    }

    /**
     * Returns the file dispersion.
     *
     * @return string
     */
    public function getDispersion()
    {
        return $this->dispersion;
    }

    /**
     * Sets the file dispersion.
     *
     * @param  string  $dispersion
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function setDispersion($dispersion)
    {
        $this->dispersion = $dispersion;

        return $this;
    }

    /**
     * Returns the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return array_get($this->config, 'default');
    }

    /**
     * Sets the default connection.
     *
     * @param  string  $connection
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function setDefaultConnection($connection)
    {
        array_set($this->config, 'default', $connection);

        return $this;
    }

    /**
     * Returns the given connection name configuration.
     *
     * @param  string  $name
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getConnectionConfig($name)
    {
        if (! $config = array_get($this->config, "connections.{$name}")) {
            throw new InvalidArgumentException("Connection [{$name}] wasn't found!");
        }

        return $config;
    }

    /**
     * Dynamically pass missing methods to connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
