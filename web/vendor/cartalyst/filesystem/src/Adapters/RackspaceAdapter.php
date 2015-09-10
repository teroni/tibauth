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

use OpenCloud\OpenStack;
use OpenCloud\ObjectStore\Resource\Container;
use League\Flysystem\Rackspace\RackspaceAdapter as Rackspace;

class RackspaceAdapter implements AdapterInterface
{

    use ValidatorTrait;

    /**
     * Required fields.
     *
     * @var array
     */
    protected $required = [
        'username',
        'password',
        'endpoint',
        'container',
    ];

    /**
     * {@inheritDoc}
     */
    public function connect(array $config)
    {
        $username = array_get($config, 'username');

        $password = array_get($config, 'password');

        $endpoint = array_get($config, 'endpoint');

        $container = array_get($config, 'container');

        $service = array_get($config, 'service');

        $region = array_get($config, 'region');

        $this->validate($config);

        $stack = $this->createOpenStack($endpoint, compact('username', 'password'));

        $store = $stack->objectStoreService($service, $region);

        $client = $store->getContainer($container);

        return new Rackspace($client);
    }

    /**
     * Creates an OpenStack instance.
     *
     * @param  string  $endpoint
     * @param  array  $credentials
     * @return \OpenCloud\OpenStack
     */
    public function createOpenStack($endpoint, $credentials)
    {
        return new OpenStack($endpoint, $credentials);
    }
}
