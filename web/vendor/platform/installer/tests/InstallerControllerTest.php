<?php namespace Platform\Installer\Tests;
/**
 * Part of the Platform Installer extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Installer extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Installer\Controllers\InstallerController;

class InstallerControllerTest extends IlluminateTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		$this->platform   = m::mock('Platform\Foundation\Platform');
		$this->installer  = m::mock('Platform\Installer\Installer');
		$this->filesystem = m::mock('Illuminate\Filesystem\Filesystem');

		$this->app['router'] = m::mock('Illuminate\Routing\Router');
		$this->app['router']->shouldReceive('filter')
			->once();

		InstallerController::setRouter($this->app['router']);

		// Controller
		$this->controller = new InstallerController($this->platform, $this->installer, $this->filesystem);
	}

	/** @test */
	public function index_route()
	{
		$requirements = [
			new \Platform\Installer\Requirements\ConfigPermissionsRequirement,
		];

		$this->app['view']->shouldReceive('make')
			->with('platform/installer::configure', ['drivers' => '', 'requirements'=> $requirements, 'pass' => false], [])
			->once();

		$this->app['path.storage'] = __DIR__;

		$this->installer->shouldReceive('getDatabaseDrivers')
			->once();

		$this->controller->index();
	}

	/** @test */
	public function configure_route()
	{
		$this->app['request']->shouldReceive('input')
			->with('user', [])
			->once()
			->andReturn([]);

		$this->app['request']->shouldReceive('input')
			->with('database.driver')
			->once()
			->andReturn('mysql');

		$this->app['request']->shouldReceive('input')
			->with('database.mysql', [])
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setUserData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setDatabaseData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('validate')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->installer->shouldReceive('install')
			->once();

		$this->redirect('to');

		$this->controller->configure();
	}

	/** @test */
	public function configure_route_errors()
	{
		$this->app['request']->shouldReceive('input')
			->with('user', [])
			->once()
			->andReturn([]);

		$this->app['request']->shouldReceive('input')
			->with('database.driver')
			->once()
			->andReturn('mysql');

		$this->app['request']->shouldReceive('input')
			->with('database.mysql', [])
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setUserData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setDatabaseData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('validate')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->once();

		$this->redirect('back')
			->redirect('withLicense')
			->redirect('withInput')
			->redirect('withErrors');

		$this->controller->configure();
	}

	/** @test */
	public function configure_route_runtime_error()
	{
		$this->app['request']->shouldReceive('input')
			->with('user', [])
			->once()
			->andReturn([]);

		$this->app['request']->shouldReceive('input')
			->with('database.driver')
			->once()
			->andReturn('mysql');

		$this->app['request']->shouldReceive('input')
			->with('database.mysql', [])
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setUserData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('setDatabaseData')
			->once()
			->andReturn([]);

		$this->installer->shouldReceive('validate')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->redirect('back')
			->redirect('withLicense')
			->redirect('withInput')
			->redirect('withErrors');

		$this->controller->configure();
	}

	/** @test */
	public function complete_route()
	{
		$this->app['view']->shouldReceive('make')
			->with('platform/installer::complete', [], [])
			->once();

		$this->controller->complete();
	}

}
