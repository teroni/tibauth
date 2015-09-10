<?php

/**
 * Part of the Permissions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Permissions
 * @version    1.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Permissions\Tests;

use PHPUnit_Framework_TestCase;
use Cartalyst\Permissions\Container;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * The permissions container instance.
     *
     * @var \Cartalyst\Permissions\Container
     */
    protected $container;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->container = new Container('main');
    }

    /** @test */
    public function a_container_can_be_instantiated()
    {
        $container = new Container('main');

        $this->assertInstanceOf('Cartalyst\Permissions\Container', $container);
    }

    /** @test */
    public function a_container_can_be_instantiated_and_have_attributes()
    {
        $container = new Container('foo');
        $container->name = 'Foo';
        $this->assertEquals('foo', $container->id);
        $this->assertEquals('Foo', $container->name);

        $container = new Container('foo', function ($container) {
            $container->name = 'Foo';
        });
        $this->assertEquals('foo', $container->id);
        $this->assertEquals('Foo', $container->name);
    }

    /** @test */
    public function a_container_group_can_be_instantiated_and_have_attributes()
    {
        $group = $this->container->group('foo');
        $group->name = 'Foo';
        $group->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $group->name);
        $this->assertEquals('Foo bar baz bat', $group->info);


        $group = $this->container->group('foo', function ($group) {
            $group->name = 'Foo';
            $group->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $group->name);
        $this->assertEquals('Foo bar baz bat', $group->info);
    }

    /** @test */
    public function a_container_can_have_a_single_group()
    {
        $this->container->group('foo');

        $this->assertCount(1, $this->container);
        $this->assertFalse($this->container->isEmpty());
        $this->assertTrue($this->container->hasGroups());
    }

    /** @test */
    public function a_container_can_have_multiple_groups()
    {
        $this->container->group('foo');
        $this->container->group('bar');
        $this->container->group('baz');

        $this->assertCount(3, $this->container);
        $this->assertFalse($this->container->isEmpty());
        $this->assertTrue($this->container->hasGroups());
    }

    /** @test */
    public function it_can_check_if_a_group_exists()
    {
        $this->container->group('foo');
        $this->container->group('bar');
        $this->container->group('baz');

        $this->assertTrue($this->container->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_group_instance()
    {
        $this->container->group('foo');
        $this->container->group('bar');
        $this->container->group('baz');

        $this->assertEquals('foo', $this->container->group('foo')->id);
    }

    /** @test */
    public function a_group_can_be_removed()
    {
        $this->container->group('foo');
        $this->container->group('bar');
        $this->container->group('baz');

        $this->assertCount(3, $this->container);
        $this->assertTrue($this->container->hasGroups());
        $this->assertEquals('foo', $this->container->first()->id);
        $this->assertEquals('baz', $this->container->last()->id);

        $this->container->pull('baz');

        $this->assertCount(2, $this->container);
        $this->assertTrue($this->container->hasGroups());
        $this->assertEquals('foo', $this->container->first()->id);
        $this->assertEquals('bar', $this->container->last()->id);
    }

    /** @test */
    public function an_existing_group_attributes_can_be_updated()
    {
        $this->container->group('foo', function ($group) {
            $group->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->container->group('foo')->name);


        $group = $this->container->group('foo');
        $group->name = 'Fooo';
        $this->assertEquals('Fooo', $this->container->group('foo')->name);


        $group = $this->container->group('foo', function ($group) {
            $group->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->container->group('foo')->name);
    }
}
