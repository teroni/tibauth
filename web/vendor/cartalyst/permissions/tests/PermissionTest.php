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
use Cartalyst\Permissions\Permission;

class PermissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * The permissions permission instance.
     *
     * @var \Cartalyst\Permissions\Permission
     */
    protected $permission;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->permission = new Permission('main');
    }

    /** @test */
    public function a_permission_can_be_instantiated()
    {
        $permission = new Permission('main');

        $this->assertInstanceOf('Cartalyst\Permissions\Permission', $permission);
    }

    /** @test */
    public function a_permission_can_be_instantiated_and_have_attributes()
    {
        $permission = new Permission('foo');
        $permission->name = 'Foo';
        $this->assertEquals('foo', $permission->id);
        $this->assertEquals('Foo', $permission->name);

        $permission = new Permission('foo', function ($permission) {
            $permission->name = 'Foo';
        });
        $this->assertEquals('foo', $permission->id);
        $this->assertEquals('Foo', $permission->name);
    }

    /** @test */
    public function a_permission_can_have_a_controller_with_methods()
    {
        $permission = new Permission('foo');
        $permission->controller('FooController', 'foo, bar');

        $this->assertEquals('FooController', $permission->controller);
        $this->assertEquals(['foo', 'bar'], $permission->methods);

        $permission = new Permission('foo');
        $permission->controller('FooController', 'foo,bar');

        $this->assertEquals('FooController', $permission->controller);
        $this->assertEquals(['foo', 'bar'], $permission->methods);

        $permission = new Permission('foo');
        $permission->controller('BarController', ['foo', 'bar', 'baz']);

        $this->assertEquals('BarController', $permission->controller);
        $this->assertEquals(['foo', 'bar', 'baz'], $permission->methods);
    }

    /** @test */
    public function a_permission_can_have_a_controller_without_methods()
    {
        $permission = new Permission('foo');
        $permission->controller('FooController');

        $this->assertEquals('FooController', $permission->controller);
        $this->assertEquals([], $permission->methods);

        $permission = new Permission('foo');
        $permission->controller('FooController');

        $this->assertEquals('FooController', $permission->controller);
        $this->assertEquals([], $permission->methods);

        $permission = new Permission('foo');
        $permission->controller('BarController');

        $this->assertEquals('BarController', $permission->controller);
        $this->assertEquals([], $permission->methods);
    }
}
