<?php namespace Platform\Foundation\Tests;
/**
 * Part of the Platform Foundation extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Foundation extension
 * @version    2.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Foundation\Controllers\Controller;

class ControllerTest extends IlluminateTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		// Foundation Controller expectations
		$this->app['sentinel']->shouldReceive('getUser');
		$this->app['view']->shouldReceive('share');
	}

	/** @test */
	public function it_can_instantiate()
	{
		$controller = new Controller();
	}

}
