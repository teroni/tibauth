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

use Cartalyst\Settings\Field;
use Cartalyst\Settings\Option;
use PHPUnit_Framework_TestCase;

class FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * The settings field instance.
     *
     * @var \Cartalyst\Settings\Field
     */
    protected $field;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->field = new Field('main');
    }

    /** @test */
    public function a_field_can_be_instantiated()
    {
        $field = new Field('main');

        $this->assertTrue($field->isEmpty());
        $this->assertFalse($field->hasOptions());
        $this->assertEquals(0, $field->count());
        $this->assertInstanceOf('Cartalyst\Settings\Field', $field);
    }

    /** @test */
    public function a_field_can_be_instantiated_and_have_attributes()
    {
        $field = new Field('foo');
        $field->name = 'Foo';
        $this->assertEquals('foo', $field->id);
        $this->assertEquals('Foo', $field->name);

        $field = new Field('foo', function ($field) {
            $field->name = 'Foo';
        });
        $this->assertEquals('foo', $field->id);
        $this->assertEquals('Foo', $field->name);
    }

    /** @test */
    public function a_field_option_can_be_instantiated_and_have_attributes()
    {
        $option = $this->field->option('foo');
        $option->name = 'Foo';
        $option->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $option->name);
        $this->assertEquals('Foo bar baz bat', $option->info);


        $option = $this->field->option('foo', function ($option) {
            $option->name = 'Foo';
            $option->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $option->name);
        $this->assertEquals('Foo bar baz bat', $option->info);
    }

    /** @test */
    public function a_field_can_have_a_single_option()
    {
        $this->field->option('foo');

        $this->assertFalse($this->field->isEmpty());
        $this->assertTrue($this->field->hasOptions());
        $this->assertEquals(1, $this->field->count());
    }

    /** @test */
    public function a_field_can_have_multiple_options()
    {
        $this->field->option('foo');
        $this->field->option('bar');
        $this->field->option('baz');

        $this->assertFalse($this->field->isEmpty());
        $this->assertTrue($this->field->hasOptions());
        $this->assertEquals(3, $this->field->count());
    }

    /** @test */
    public function it_can_check_if_a_option_exists()
    {
        $this->field->option('foo');
        $this->field->option('bar');
        $this->field->option('baz');

        $this->assertTrue($this->field->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_option_instance()
    {
        $this->field->option('foo');
        $this->field->option('bar');
        $this->field->option('baz');

        $this->assertEquals('foo', $this->field->option('foo')->id);
    }

    /** @test */
    public function a_option_can_be_removed()
    {
        $this->field->option('foo');
        $this->field->option('bar');
        $this->field->option('baz');

        $this->assertTrue($this->field->hasOptions());
        $this->assertEquals(3, $this->field->count());
        $this->assertEquals('foo', $this->field->first()->id);
        $this->assertEquals('baz', $this->field->last()->id);

        $this->field->pull('baz');

        $this->assertTrue($this->field->hasOptions());
        $this->assertEquals(2, $this->field->count());
        $this->assertEquals('foo', $this->field->first()->id);
        $this->assertEquals('bar', $this->field->last()->id);
    }

    /** @test */
    public function an_existing_option_attributes_can_be_updated()
    {
        $this->field->option('foo', function ($option) {
            $option->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->field->option('foo')->name);


        $option = $this->field->option('foo');
        $option->name = 'Fooo';
        $this->assertEquals('Fooo', $this->field->option('foo')->name);


        $option = $this->field->option('foo', function ($option) {
            $option->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->field->option('foo')->name);
    }

    /** @test */
    public function it_can_return_the_correct_field_type()
    {
        # 1)
        $field = new Field('foo');
        $this->assertEquals('text', $field->type);

        # 2)
        $field = new Field('foo');
        $field->type = 'radio';
        $this->assertEquals('radio', $field->type);

        $field = new Field('foo', function ($field) {
            $field->type = 'radio';
        });
        $this->assertEquals('radio', $field->type);

        # 3)
        $field = new Field('foo');
        $field->type = 'checkbox';
        $field->option('bar');
        $field->option('baz');
        $this->assertEquals('checkbox', $field->type);

        $field = new Field('foo', function ($field) {
            $field->type = 'checkbox';
        });

        $field->option('bar');
        $field->option('baz');
        $this->assertEquals('checkbox', $field->type);


        # 4)
        $field = new Field('foo');
        $field->type = 'text';
        $field->option('bar');
        $field->option('baz');
        $this->assertEquals('select', $field->type);

        $field = new Field('foo', function ($field) {
            $field->type = 'text';
        });
        $field->option('bar');
        $field->option('baz');
        $this->assertEquals('select', $field->type);
    }

    /** @test */
    public function it_can_attach_an_option_to_the_field()
    {
        $this->assertCount(0, $this->field);

        $this->field->attach(new Option('foo'));
        $this->field->attach(new Option('bar'));
        $this->field->attach(new Option('baz'));
        $this->field->attach(new Option('foo'));
        $this->field->attach(new Field('foo'));

        $this->assertCount(3, $this->field);
    }
}
