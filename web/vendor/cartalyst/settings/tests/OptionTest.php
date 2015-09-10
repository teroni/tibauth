<?php

/**
 * Part of the Settings package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Settings
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Settings\Tests;

use Cartalyst\Settings\Option;
use PHPUnit_Framework_TestCase;

class OptionTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function a_option_can_be_instantiated()
    {
        $option = new Option('foo');

        $this->assertEquals('foo', $option->id);
        $this->assertInstanceOf('Cartalyst\Settings\Option', $option);
    }

    /** @test */
    public function a_option_can_be_instantiated_with_a_closure_as_second_argument()
    {
        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
        });
        $this->assertEquals('foo', $option->id);
        $this->assertEquals('Foo', $option->name);
        $this->assertInstanceOf('Cartalyst\Settings\Option', $option);
    }

    /** @test */
    public function a_option_value_can_be_boolean_or_integer_and_return_the_correct_type()
    {
        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
            $option->value = true;
        });
        $this->assertEquals(1, $option->value);

        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
            $option->value = 1;
        });
        $this->assertEquals(1, $option->value);

        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
            $option->value = false;
        });
        $this->assertEquals(0, $option->value);

        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
            $option->value = 0;
        });
        $this->assertEquals(0, $option->value);

        $option = new Option('foo', function ($option) {
            $option->name = 'Foo';
            $option->value = -1;
        });
        $this->assertEquals(-1, $option->value);
    }
}
