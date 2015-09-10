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
use PHPUnit_Framework_TestCase;
use Cartalyst\Settings\Fieldset;

class FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * The settings fieldset instance.
     *
     * @var \Cartalyst\Settings\Fieldset
     */
    protected $fieldset;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->fieldset = new Fieldset('main');
    }

    /** @test */
    public function a_fieldset_can_be_instantiated()
    {
        $fieldset = new Fieldset('main');

        $this->assertTrue($fieldset->isEmpty());
        $this->assertFalse($fieldset->hasFields());
        $this->assertEquals(0, $fieldset->count());
        $this->assertInstanceOf('Cartalyst\Settings\Fieldset', $fieldset);
    }

    /** @test */
    public function a_fieldset_can_be_instantiated_and_have_attributes()
    {
        $fieldset = new Fieldset('foo');
        $fieldset->name = 'Foo';
        $this->assertEquals('foo', $fieldset->id);
        $this->assertEquals('Foo', $fieldset->name);

        $fieldset = new Fieldset('foo', function ($fieldset) {
            $fieldset->name = 'Foo';
        });
        $this->assertEquals('foo', $fieldset->id);
        $this->assertEquals('Foo', $fieldset->name);
    }

    /** @test */
    public function a_fieldset_field_can_be_instantiated_and_have_attributes()
    {
        $field = $this->fieldset->field('foo');
        $field->name = 'Foo';
        $field->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $field->name);
        $this->assertEquals('Foo bar baz bat', $field->info);


        $field = $this->fieldset->field('foo', function ($field) {
            $field->name = 'Foo';
            $field->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $field->name);
        $this->assertEquals('Foo bar baz bat', $field->info);
    }

    /** @test */
    public function a_fieldset_can_have_a_single_field()
    {
        $this->fieldset->field('foo');

        $this->assertFalse($this->fieldset->isEmpty());
        $this->assertTrue($this->fieldset->hasFields());
        $this->assertEquals(1, $this->fieldset->count());
    }

    /** @test */
    public function a_fieldset_can_have_multiple_fields()
    {
        $this->fieldset->field('foo');
        $this->fieldset->field('bar');
        $this->fieldset->field('baz');

        $this->assertFalse($this->fieldset->isEmpty());
        $this->assertTrue($this->fieldset->hasFields());
        $this->assertEquals(3, $this->fieldset->count());
    }

    /** @test */
    public function it_can_check_if_a_field_exists()
    {
        $this->fieldset->field('foo');
        $this->fieldset->field('bar');
        $this->fieldset->field('baz');

        $this->assertTrue($this->fieldset->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_field_instance()
    {
        $this->fieldset->field('foo');
        $this->fieldset->field('bar');
        $this->fieldset->field('baz');

        $this->assertEquals('foo', $this->fieldset->field('foo')->id);
    }

    /** @test */
    public function a_field_can_be_removed()
    {
        $this->fieldset->field('foo');
        $this->fieldset->field('bar');
        $this->fieldset->field('baz');

        $this->assertTrue($this->fieldset->hasFields());
        $this->assertEquals(3, $this->fieldset->count());
        $this->assertEquals('foo', $this->fieldset->first()->id);
        $this->assertEquals('baz', $this->fieldset->last()->id);

        $this->fieldset->pull('baz');

        $this->assertTrue($this->fieldset->hasFields());
        $this->assertEquals(2, $this->fieldset->count());
        $this->assertEquals('foo', $this->fieldset->first()->id);
        $this->assertEquals('bar', $this->fieldset->last()->id);
    }

    /** @test */
    public function an_existing_field_attributes_can_be_updated()
    {
        $this->fieldset->field('foo', function ($field) {
            $field->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->fieldset->field('foo')->name);


        $field = $this->fieldset->field('foo');
        $field->name = 'Fooo';
        $this->assertEquals('Fooo', $this->fieldset->field('foo')->name);


        $field = $this->fieldset->field('foo', function ($field) {
            $field->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->fieldset->field('foo')->name);
    }

    /** @test */
    public function a_fieldset_field_can_have_a_permission()
    {
        $fieldset = new Fieldset('foo', function ($fieldset) {
            $fieldset->field('foo', function ($field) {
                $field->permission(function () {
                    return false;
                });
            });
            $fieldset->field('bar');
            $fieldset->field('baz');
        });

        $fieldset->beforeCallback();
    }

    /** @test */
    public function it_can_attach_a_field_to_the_fieldset()
    {
        $this->assertCount(0, $this->fieldset);

        $this->fieldset->attach(new Field('foo'));
        $this->fieldset->attach(new Field('bar'));
        $this->fieldset->attach(new Field('baz'));
        $this->fieldset->attach(new Field('foo'));
        $this->fieldset->attach(new Fieldset('foo'));

        $this->assertCount(3, $this->fieldset);
    }
}
