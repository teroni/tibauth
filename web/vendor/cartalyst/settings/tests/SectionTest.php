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

use Cartalyst\Settings\Section;
use PHPUnit_Framework_TestCase;
use Cartalyst\Settings\Fieldset;

class SectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * The settings section instance.
     *
     * @var \Cartalyst\Settings\Section
     */
    protected $section;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->section = new Section('main');
    }

    /** @test */
    public function a_section_can_be_instantiated()
    {
        $section = new Section('main');

        $this->assertTrue($section->isEmpty());
        $this->assertFalse($section->hasFieldsets());
        $this->assertEquals(0, $section->count());
        $this->assertInstanceOf('Cartalyst\Settings\Section', $section);
    }

    /** @test */
    public function a_section_can_be_instantiated_and_have_attributes()
    {
        $section = new Section('foo');
        $section->name = 'Foo';
        $this->assertEquals('foo', $section->id);
        $this->assertEquals('Foo', $section->name);

        $section = new Section('foo', function ($section) {
            $section->name = 'Foo';
        });
        $this->assertEquals('foo', $section->id);
        $this->assertEquals('Foo', $section->name);
    }

    /** @test */
    public function a_section_fieldset_can_be_instantiated_and_have_attributes()
    {
        $fieldset = $this->section->fieldset('foo');
        $fieldset->name = 'Foo';
        $fieldset->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $fieldset->name);
        $this->assertEquals('Foo bar baz bat', $fieldset->info);


        $fieldset = $this->section->fieldset('foo', function ($fieldset) {
            $fieldset->name = 'Foo';
            $fieldset->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $fieldset->name);
        $this->assertEquals('Foo bar baz bat', $fieldset->info);
    }

    /** @test */
    public function a_section_can_have_a_single_fieldset()
    {
        $this->section->fieldset('foo');

        $this->assertFalse($this->section->isEmpty());
        $this->assertTrue($this->section->hasFieldsets());
        $this->assertEquals(1, $this->section->count());
    }

    /** @test */
    public function a_section_can_have_multiple_fieldsets()
    {
        $this->section->fieldset('foo');
        $this->section->fieldset('bar');
        $this->section->fieldset('baz');

        $this->assertFalse($this->section->isEmpty());
        $this->assertTrue($this->section->hasFieldsets());
        $this->assertEquals(3, $this->section->count());
    }

    /** @test */
    public function it_can_check_if_a_fieldset_exists()
    {
        $this->section->fieldset('foo');
        $this->section->fieldset('bar');
        $this->section->fieldset('baz');

        $this->assertTrue($this->section->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_fieldset_instance()
    {
        $this->section->fieldset('foo');
        $this->section->fieldset('bar');
        $this->section->fieldset('baz');

        $this->assertEquals('foo', $this->section->fieldset('foo')->id);
    }

    /** @test */
    public function a_fieldset_can_be_removed()
    {
        $this->section->fieldset('foo');
        $this->section->fieldset('bar');
        $this->section->fieldset('baz');

        $this->assertTrue($this->section->hasFieldsets());
        $this->assertEquals(3, $this->section->count());
        $this->assertEquals('foo', $this->section->first()->id);
        $this->assertEquals('baz', $this->section->last()->id);

        $this->section->pull('baz');

        $this->assertTrue($this->section->hasFieldsets());
        $this->assertEquals(2, $this->section->count());
        $this->assertEquals('foo', $this->section->first()->id);
        $this->assertEquals('bar', $this->section->last()->id);
    }

    /** @test */
    public function an_existing_fieldset_attributes_can_be_updated()
    {
        $this->section->fieldset('foo', function ($fieldset) {
            $fieldset->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->section->fieldset('foo')->name);


        $fieldset = $this->section->fieldset('foo');
        $fieldset->name = 'Fooo';
        $this->assertEquals('Fooo', $this->section->fieldset('foo')->name);


        $fieldset = $this->section->fieldset('foo', function ($fieldset) {
            $fieldset->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->section->fieldset('foo')->name);
    }

    /** @test */
    public function a_section_fieldset_can_have_a_permission()
    {
        $section = new Section('foo', function ($section) {
            $section->fieldset('foo', function ($fieldset) {
                $fieldset->permission(function () {
                    return false;
                });
            });
            $section->fieldset('bar');
            $section->fieldset('baz');
        });

        $section->beforeCallback();
    }

    /** @test */
    public function it_can_check_if_any_fieldset_on_a_section_has_any_fields()
    {
        $section = new Section('foo', function ($section) {
            $section->fieldset('foo');
            $section->fieldset('bar');
            $section->fieldset('baz');
        });
        $this->assertFalse($section->anyFieldsetHasFields());

        $section = new Section('foo', function ($section) {
            $section->fieldset('foo', function ($fieldset) {
                $fieldset->field('foo');
            });
            $section->fieldset('bar');
            $section->fieldset('baz');
        });
        $this->assertTrue($section->anyFieldsetHasFields());
    }

    /** @test */
    public function it_can_attach_a_fieldset_to_the_section()
    {
        $this->assertCount(0, $this->section);

        $this->section->attach(new Fieldset('foo'));
        $this->section->attach(new Fieldset('bar'));
        $this->section->attach(new Fieldset('baz'));
        $this->section->attach(new Fieldset('foo'));
        $this->section->attach(new Section('foo'));

        $this->assertCount(3, $this->section);
    }
}
