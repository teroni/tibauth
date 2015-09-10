<?php

/**
 * Part of the Extensions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Extensions
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Extensions\Tests;

use Mockery as m;
use Cartalyst\Extensions\FileFinder;

class FileFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_can_find_extensions_in_a_path()
    {
        $finder = new FileFinder(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $paths = array('foo')
        );

        $filesystem->shouldReceive('glob')->with('foo/*/*/extension.php')->once()->andReturn(false);
        $this->assertEquals(array(), $finder->findExtensionsInPath('foo'));

        $filesystem->shouldReceive('glob')->with('bar/*/*/extension.php')->once()->andReturn($expected = array(
            'bar/baz/qux/extension.php',
        ));
        $this->assertEquals($expected, $finder->findExtensionsInPath('bar'));
    }

    /** @test */
    public function it_can_find_extensions()
    {
        $finder = m::mock('Cartalyst\Extensions\FileFinder[findExtensionsInPath]');
        $finder->__construct(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $paths = array('foo')
        );
        $finder->shouldReceive('findExtensionsInPath')->with('foo')->twice()->andReturn(array('bar'));

        $this->assertEquals(array('bar'), $finder->findExtensions());

        $finder->addPath('baz');
        $finder->shouldReceive('findExtensionsInPath')->with('baz')->once()->andReturn(array('qux'));

        $this->assertEquals(array('bar', 'qux'), $finder->findExtensions());
    }
}
