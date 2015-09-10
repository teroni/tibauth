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

use Cartalyst\Settings\Form;
use Cartalyst\Settings\Section;
use PHPUnit_Framework_TestCase;

class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * The settings form instance.
     *
     * @var \Cartalyst\Settings\Form
     */
    protected $form;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->form = new Form('main');
    }

    /** @test */
    public function a_form_can_be_instantiated()
    {
        $form = new Form('main');

        $this->assertTrue($form->isEmpty());
        $this->assertFalse($form->hasSections());
        $this->assertEquals(0, $form->count());
        $this->assertInstanceOf('Cartalyst\Settings\Form', $form);
    }

    /** @test */
    public function a_form_can_be_instantiated_and_have_attributes()
    {
        $form = new Form('foo');
        $form->name = 'Foo';
        $this->assertEquals('foo', $form->id);
        $this->assertEquals('Foo', $form->name);

        $form = new Form('foo', function ($form) {
            $form->name = 'Foo';
        });
        $this->assertEquals('foo', $form->id);
        $this->assertEquals('Foo', $form->name);
    }

    /** @test */
    public function a_form_section_can_be_instantiated_and_have_attributes()
    {
        $section = $this->form->section('foo');
        $section->name = 'Foo';
        $section->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $section->name);
        $this->assertEquals('Foo bar baz bat', $section->info);


        $section = $this->form->section('foo', function ($section) {
            $section->name = 'Foo';
            $section->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $section->name);
        $this->assertEquals('Foo bar baz bat', $section->info);
    }

    /** @test */
    public function a_form_can_have_a_single_section()
    {
        $this->form->section('foo');

        $this->assertFalse($this->form->isEmpty());
        $this->assertTrue($this->form->hasSections());
        $this->assertEquals(1, $this->form->count());
    }

    /** @test */
    public function a_form_can_have_multiple_sections()
    {
        $this->form->section('foo');
        $this->form->section('bar');
        $this->form->section('baz');

        $this->assertFalse($this->form->isEmpty());
        $this->assertTrue($this->form->hasSections());
        $this->assertEquals(3, $this->form->count());
    }

    /** @test */
    public function it_can_check_if_a_section_exists()
    {
        $this->form->section('foo');
        $this->form->section('bar');
        $this->form->section('baz');

        $this->assertTrue($this->form->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_section_instance()
    {
        $this->form->section('foo');
        $this->form->section('bar');
        $this->form->section('baz');

        $this->assertEquals('foo', $this->form->section('foo')->id);
    }

    /** @test */
    public function a_section_can_be_removed()
    {
        $this->form->section('foo');
        $this->form->section('bar');
        $this->form->section('baz');

        $this->assertTrue($this->form->hasSections());
        $this->assertEquals(3, $this->form->count());
        $this->assertEquals('foo', $this->form->first()->id);
        $this->assertEquals('baz', $this->form->last()->id);

        $this->form->pull('baz');

        $this->assertTrue($this->form->hasSections());
        $this->assertEquals(2, $this->form->count());
        $this->assertEquals('foo', $this->form->first()->id);
        $this->assertEquals('bar', $this->form->last()->id);
    }

    /** @test */
    public function an_existing_section_attributes_can_be_updated()
    {
        $this->form->section('foo', function ($section) {
            $section->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->form->section('foo')->name);


        $section = $this->form->section('foo');
        $section->name = 'Fooo';
        $this->assertEquals('Fooo', $this->form->section('foo')->name);


        $section = $this->form->section('foo', function ($section) {
            $section->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->form->section('foo')->name);
    }

    /** @test */
    public function a_form_section_can_have_a_permission()
    {
        $form = new Form('foo', function ($form) {
            $form->section('foo', function ($section) {
                $section->permission(function () {
                    return false;
                });
            });
            $form->section('bar');
            $form->section('baz');
        });

        $form->beforeCallback();
    }

    /** @test */
    public function it_can_attach_a_section_to_the_form()
    {
        $this->assertCount(0, $this->form);

        $this->form->attach(new Section('foo'));
        $this->form->attach(new Section('bar'));
        $this->form->attach(new Section('baz'));
        $this->form->attach(new Section('foo'));
        $this->form->attach(new Form('foo'));

        $this->assertCount(3, $this->form);
    }
}
