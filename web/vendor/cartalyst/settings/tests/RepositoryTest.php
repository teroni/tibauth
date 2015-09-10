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
use PHPUnit_Framework_TestCase;
use Cartalyst\Settings\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * The settings repository instance.
     *
     * @var \Cartalyst\Settings\Repository
     */
    protected $repository;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->repository = new Repository('main');
    }

    /** @test */
    public function a_repository_can_be_instantiated()
    {
        $repository = new Repository('main');

        $this->assertCount(0, $repository);
        $this->assertTrue($repository->isEmpty());
        $this->assertFalse($repository->hasForms());
        $this->assertInstanceOf('Cartalyst\Settings\Repository', $repository);
    }

    /** @test */
    public function a_repository_can_be_instantiated_and_have_attributes()
    {
        $repository = new Repository('foo');
        $repository->name = 'Foo';
        $this->assertEquals('foo', $repository->id);
        $this->assertEquals('Foo', $repository->name);

        $repository = new Repository('foo', function ($repository) {
            $repository->name = 'Foo';
        });
        $this->assertEquals('foo', $repository->id);
        $this->assertEquals('Foo', $repository->name);
    }

    /** @test */
    public function a_repository_form_can_be_instantiated_and_have_attributes()
    {
        $form = $this->repository->form('foo');
        $form->name = 'Foo';
        $form->info = 'Foo bar baz bat';
        $this->assertEquals('Foo', $form->name);
        $this->assertEquals('Foo bar baz bat', $form->info);


        $form = $this->repository->form('foo', function ($form) {
            $form->name = 'Foo';
            $form->info = 'Foo bar baz bat';
        });
        $this->assertEquals('Foo', $form->name);
        $this->assertEquals('Foo bar baz bat', $form->info);
    }

    /** @test */
    public function a_repository_can_have_a_single_form()
    {
        $this->repository->form('foo');

        $this->assertCount(1, $this->repository);
        $this->assertFalse($this->repository->isEmpty());
        $this->assertTrue($this->repository->hasForms());
    }

    /** @test */
    public function a_repository_can_have_multiple_forms()
    {
        $this->repository->form('foo');
        $this->repository->form('bar');
        $this->repository->form('baz');

        $this->assertCount(3, $this->repository);
        $this->assertFalse($this->repository->isEmpty());
        $this->assertTrue($this->repository->hasForms());
    }

    /** @test */
    public function it_can_check_if_a_form_exists()
    {
        $this->repository->form('foo');
        $this->repository->form('bar');
        $this->repository->form('baz');

        $this->assertTrue($this->repository->has('bar'));
    }

    /** @test */
    public function it_can_return_an_existing_form_instance()
    {
        $this->repository->form('foo');
        $this->repository->form('bar');
        $this->repository->form('baz');

        $this->assertEquals('foo', $this->repository->form('foo')->id);
    }

    /** @test */
    public function a_form_can_be_removed()
    {
        $this->repository->form('foo');
        $this->repository->form('bar');
        $this->repository->form('baz');

        $this->assertCount(3, $this->repository);
        $this->assertTrue($this->repository->hasForms());
        $this->assertEquals('foo', $this->repository->first()->id);
        $this->assertEquals('baz', $this->repository->last()->id);

        $this->repository->pull('baz');

        $this->assertCount(2, $this->repository);
        $this->assertTrue($this->repository->hasForms());
        $this->assertEquals('foo', $this->repository->first()->id);
        $this->assertEquals('bar', $this->repository->last()->id);
    }

    /** @test */
    public function an_existing_form_attributes_can_be_updated()
    {
        $this->repository->form('foo', function ($form) {
            $form->name = 'Foo';
        });
        $this->assertEquals('Foo', $this->repository->form('foo')->name);


        $form = $this->repository->form('foo');
        $form->name = 'Fooo';
        $this->assertEquals('Fooo', $this->repository->form('foo')->name);


        $form = $this->repository->form('foo', function ($form) {
            $form->name = 'Foooo';
        });
        $this->assertEquals('Foooo', $this->repository->form('foo')->name);
    }

    /** @test */
    public function it_can_attach_a_form_to_the_repository()
    {
        $this->assertCount(0, $this->repository);

        $this->repository->attach(new Form('foo'));
        $this->repository->attach(new Form('bar'));
        $this->repository->attach(new Form('baz'));
        $this->repository->attach(new Form('foo'));
        $this->repository->attach(new Repository('foo'));

        $this->assertCount(3, $this->repository);
    }
}
