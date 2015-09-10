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
use Cartalyst\Permissions\Group;

class GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * The permissions group instance.
     *
     * @var \Cartalyst\Permissions\Group
     */
    protected $group;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->group = new Group('main');
    }

    /** @test */
    public function a_group_can_be_instantiated()
    {
        $group = new Group('main');

        $this->assertInstanceOf('Cartalyst\Permissions\Group', $group);
    }

    /** @test */
    public function a_group_can_be_instantiated_and_have_attributes()
    {
        $group = new Group('foo');
        $group->name = 'Foo';
        $this->assertEquals('foo', $group->id);
        $this->assertEquals('Foo', $group->name);

        $group = new Group('foo', function ($group) {
            $group->name = 'Foo';
        });
        $this->assertEquals('foo', $group->id);
        $this->assertEquals('Foo', $group->name);
    }

    /** @test */
    public function a_group_permission_can_be_instantiated_and_have_attributes()
    {
        $permission = $this->group->permission('foo');
        $permission->name = 'Foo';
        $permission->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $permission->name);
        $this->assertEquals('Foo bar baz bat', $permission->info);


        $permission = $this->group->permission('foo', function ($permission) {
            $permission->name = 'Foo';
            $permission->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $permission->name);
        $this->assertEquals('Foo bar baz bat', $permission->info);
    }

    /** @test */
    public function a_group_can_have_a_single_permission()
    {
        $this->group->permission('foo');

        $this->assertCount(1, $this->group);
        $this->assertFalse($this->group->isEmpty());
        $this->assertTrue($this->group->hasPermissions());
    }

    /** @test */
    public function a_group_can_have_multiple_permissions()
    {
        $this->group->permission('foo');
        $this->group->permission('bar');
        $this->group->permission('baz');

        $this->assertCount(3, $this->group);
        $this->assertFalse($this->group->isEmpty());
        $this->assertTrue($this->group->hasPermissions());
    }

    /** @test */
    public function it_can_check_if_a_permission_exists()
    {
        $this->group->permission('foo');
        $this->group->permission('bar');
        $this->group->permission('baz');

        $this->assertTrue($this->group->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_permission_instance()
    {
        $this->group->permission('foo');
        $this->group->permission('bar');
        $this->group->permission('baz');

        $this->assertEquals('foo', $this->group->permission('foo')->id);
    }

    /** @test */
    public function a_group_can_be_removed()
    {
        $this->group->permission('foo');
        $this->group->permission('bar');
        $this->group->permission('baz');

        $this->assertCount(3, $this->group);
        $this->assertTrue($this->group->hasPermissions());
        $this->assertEquals('foo', $this->group->first()->id);
        $this->assertEquals('baz', $this->group->last()->id);

        $this->group->pull('baz');

        $this->assertCount(2, $this->group);
        $this->assertTrue($this->group->hasPermissions());
        $this->assertEquals('foo', $this->group->first()->id);
        $this->assertEquals('bar', $this->group->last()->id);
    }

    /** @test */
    public function an_existing_permission_attributes_can_be_updated()
    {
        $this->group->permission('foo', function ($group) {
            $group->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->group->permission('foo')->name);


        $group = $this->group->permission('foo');
        $group->name = 'Fooo';
        $this->assertEquals('Fooo', $this->group->permission('foo')->name);


        $group = $this->group->permission('foo', function ($group) {
            $group->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->group->permission('foo')->name);
    }
}
