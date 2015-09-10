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

use Mockery as m;
use Cartalyst\Settings\Section;
use PHPUnit_Framework_TestCase;
use Illuminate\Validation\Factory;
use Cartalyst\Settings\SectionValidate;

class SectionValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Illuminate Validation Factory instance.
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->validator = new Factory(
            m::mock('Symfony\Component\Translation\TranslatorInterface')
        );
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $validator = new Factory(
            m::mock($interface = 'Symfony\Component\Translation\TranslatorInterface')
        );

        $this->assertInstanceOf($interface, $validator->getTranslator());
    }

    /** @test */
    public function it_can_validate_a_section()
    {
        $section = new Section('foo', function ($s) {
            $s->fieldset('foo', function ($f) {
                $f->field('foo', function ($f) {
                    $f->rules = 'required';
                });

                $f->field('bar');

                $f->field('baz');
            });

            $s->fieldset('bar');

            $s->fieldset('baz');
        });

        $fieldsetValidate = new SectionValidate($this->validator);
        $validation = $fieldsetValidate->validate($section, ['foo' => 'bar']);

        $this->assertEmpty($validation->getMessages());
    }
}
