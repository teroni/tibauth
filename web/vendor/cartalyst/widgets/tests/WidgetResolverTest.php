<?php
/**
 * Part of the Widgets package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Widgets
 * @version    1.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Illuminate\Container\Container;
use Cartalyst\Widgets\WidgetResolver;

class WidgetResolverTest extends PHPUnit_Framework_TestCase {

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
	 * @expectedException InvalidArgumentException
	 */
	public function testParsingKeyThrowsExceptionIfExtensionIsNotGivenInCorrectFormat()
	{
		$resolver = new WidgetResolver(
			$container    = new Container,
			$extensionBag = $this->getNewExtensionBagMock()
		);

		$resolver->parseKey('baz.bat.qux');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testParsingKeyThrowsExceptionIfExtensionDoesNotExist()
	{
		$resolver = new WidgetResolver(
			$container    = new Container,
			$extensionBag = $this->getNewExtensionBagMock()
		);

		$extensionBag->shouldReceive('offsetExists')->with('foo/bar')->once()->andReturn(false);

		$resolver->parseKey('foo/bar::baz.bat.qux');
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testParsingKeyThrowsExceptionIfExtensionIsNotEnabled()
	{
		$resolver = new WidgetResolver(
			$container    = new Container,
			$extensionBag = $this->getNewExtensionBagMock()
		);

		$extensionBag->shouldReceive('offsetExists')->with('foo/bar')->andReturn(true);
		$extensionBag->shouldReceive('offsetGet')->with('foo/bar')->andReturn($extension1 = m::mock('Cartalyst\Extensions\ExtensionInterface'));
		$extension1->shouldReceive('isEnabled')->andReturn(false);

		// Only used for exception
		$extension1->shouldReceive('getSlug')->once()->andReturn('foo/bar');

		$resolver->parseKey('foo/bar::baz.bat.qux');
	}

	public function testParsingKeyReturnsCorrectClass()
	{
		$resolver = new WidgetResolver(
			$container    = new Container,
			$extensionBag = $this->getNewExtensionBagMock()
		);

		$extensionBag->shouldReceive('offsetExists')->with('foo/bar')->andReturn(true);
		$extensionBag->shouldReceive('offsetGet')->with('foo/bar')->andReturn($extension1 = m::mock('Cartalyst\Extensions\ExtensionInterface'));
		$extension1->shouldReceive('getNamespace')->once()->andReturn('Foo\Bar');
		$extension1->shouldReceive('isEnabled')->once()->andReturn(true);

		// Double check we did get an array with two indexes
		$this->assertCount(2, $actual = $resolver->parseKey('foo/bar::baz.bat'));

		// Order matters so we'll inspect each individually
		$expected = array('Foo\Bar\Widgets\Baz', 'bat');
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertEquals($expected[1], $actual[1]);
	}

	public function testParsingKeyReturnsCorrectClassWithPrefix()
	{
		$resolver = new WidgetResolver(
			$container    = new Container,
			$extensionBag = $this->getNewExtensionBagMock()
		);

		$extensionBag->shouldReceive('offsetExists')->with('foo/bar')->andReturn(true);
		$extensionBag->shouldReceive('offsetGet')->with('foo/bar')->andReturn($extension1 = m::mock('Cartalyst\Extensions\ExtensionInterface'));
		$extension1->shouldReceive('getNamespace')->once()->andReturn('Foo\Bar');
		$extension1->shouldReceive('isEnabled')->once()->andReturn(true);

		// Double check we did get an array with two indexes
		$this->assertCount(2, $actual = $resolver->parseKey('foo/bar::baz.bat', 'Corge'));

		// Order matters so we'll inspect each individually
		$expected = array('Foo\Bar\Corge\Baz', 'bat');
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertEquals($expected[1], $actual[1]);
	}

	/**
	 * @return \Cartalyst\Extensions\ExtensionBag
	 */
	protected function getNewExtensionBagMock()
	{
		return m::mock('Cartalyst\Extensions\ExtensionBag', [
			m::mock('Illuminate\Filesystem\Filesystem'),
			m::mock('Cartalyst\Extensions\FinderInterface'),
			m::mock('Illuminate\Container\Container'),
			[]
		]);
	}

}
