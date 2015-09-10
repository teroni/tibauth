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
use Illuminate\Http\Request;
use Cartalyst\Settings\Section;
use PHPUnit_Framework_TestCase;
use Illuminate\Config\Repository;
use Cartalyst\Settings\SectionPrepare;

class SectionPrepareTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Illuminate Config Repository instance.
     *
     * @var \Illuminate\Config\Repository
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
        $this->config = new Repository();
    }

    /** @test */
    public function it_can_test_the_prepare_method()
    {
        $config = m::mock('Illuminate\Config\Repository');

        $config->shouldReceive('get')
            ->with('app.foo')
            ->once()
            ->andReturn('Foo');

        $config->shouldReceive('get')
            ->with('app.bar')
            ->once()
            ->andReturn('Bar');

        $config->shouldReceive('get')
            ->with('app.baz')
            ->once()
            ->andReturn('Baz');

        $request = new Request;
        $session = m::mock('Illuminate\Session\Store');
        $session->shouldReceive('getOldInput')->once()->with('foo', 'Foo')->andReturn('Foo');
        $session->shouldReceive('getOldInput')->once()->with('bar', 'Bar')->andReturn('Bar');
        $session->shouldReceive('getOldInput')->once()->with('baz', 'Baz')->andReturn('Baz');

        $request->setSession($session);
        $sectionPrepare = new SectionPrepare($config, $request);

        $section = new Section('foo', function ($s) {
            $s->fieldset('foo', function ($f) {
                $f->field('foo', function ($f) {
                    $f->config = 'app.foo';
                });
                $f->field('bar', function ($f) {
                    $f->config = 'app.bar';
                });
                $f->field('baz', function ($f) {
                    $f->config = 'app.baz';
                });
            });
            $s->fieldset('bar');
            $s->fieldset('baz');
        });

        $sectionPrepare->prepare($section);

        $this->assertEquals('Foo', $section->fieldset('foo')->field('foo')->value);
        $this->assertEquals('Bar', $section->fieldset('foo')->field('bar')->value);
        $this->assertEquals('Baz', $section->fieldset('foo')->field('baz')->value);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_an_exception_when_a_field_does_not_have_a_config_attribute()
    {
        $section = new Section('foo', function ($s) {
            $s->fieldset('foo', function ($f) {
                $f->field('foo');
            });
        });

        $sectionPrepare = new SectionPrepare(
            $this->getRepository(),
            $this->getRequest()
        );
        $sectionPrepare->prepare($section);
    }

    protected function getRepository()
    {
        return new Repository();
    }

    protected function getRequest()
    {
        return new Request;
    }
}
