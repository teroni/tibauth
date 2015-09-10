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
 * @version    3.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Filesystem\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Cartalyst\Filesystem\Adapters\RackspaceAdapter;

class RackspaceAdapterTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_connect()
    {
        $config = [
            'username'  => 'user',
            'password'  => 'secret',
            'endpoint'  => 'foo',
            'container' => 'example',
            'service'   => 'cloudFiles',
            'region'    => 'LON',
        ];

        $adapter = m::mock('Cartalyst\Filesystem\Adapters\RackspaceAdapter[createOpenStack]');

        $adapter->shouldReceive('createOpenStack')
            ->with('foo', ['username' => 'user', 'password' => 'secret'])
            ->once()
            ->andReturn($stack = m::mock('OpenCloud\OpenStack'));

        $stack->shouldReceive('objectStoreService')
            ->with('cloudFiles', 'LON')
            ->once()
            ->andReturn($store = m::mock('OpenCloud\ObjectStore\Service'));

        $store->shouldReceive('getContainer')
            ->with('example')
            ->once()
            ->andReturn($container = m::mock('OpenCloud\ObjectStore\Resource\Container'));

        $this->assertInstanceOf('League\Flysystem\Rackspace\RackspaceAdapter', $adapter->connect($config));
    }
}
