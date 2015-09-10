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

use Cartalyst\Filesystem\Adapters\AdapterFactory;
use PHPUnit_Framework_TestCase;

class AdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the adapter factory instance.
     *
     * @var \Cartalyst\Filesystem\Adapters\AdapterFactory
     */
    protected $adapter;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->adapter = new AdapterFactory();
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf('Cartalyst\Filesystem\Adapters\AdapterFactory', $this->adapter);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_invalid_argument_exception_if_no_matching_adapter_is_found()
    {
        $adapter = [
            'adapter' => 'invalid',
            'path'    => 'public/filesystem',
        ];

        $this->adapter->make($adapter);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_invalid_argument_exception_if_no_adapter_is_set()
    {
        $adapter = [
            'path' => 'public/filesystem',
        ];

        $this->adapter->make($adapter);
    }
}
