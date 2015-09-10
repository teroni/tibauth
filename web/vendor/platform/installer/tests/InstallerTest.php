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
use Platform\Installer\Installer;
use Platform\Installer\Repository;
use Illuminate\Container\Container;
use Cartalyst\Testing\IlluminateTestCase;

class InstallerTest extends IlluminateTestCase {

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$this->repository = new Repository;
		$this->installer  = new Installer($this->app, $this->repository);
	}

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
	public function it_can_set_the_user_data()
	{
		$this->installer->setUserData([
			'email'            => 'user@example.com',
			'password'         => 'secret',
			'password_confirm' => 'secret',
		]);

		$this->assertNotEmpty(array_filter($this->installer->getUserData()));
	}

	/** @test */
	public function it_can_set_the_database_data()
	{
		$this->installer->setDatabaseData('mysql', [
			'database' => 'foobar',
			'username' => 'foo',
			'password' => 'bar',
		]);

		$this->assertCount(4, $this->installer->getDatabaseData());

		$this->assertCount(7, $this->installer->getDatabaseData('mysql'));
	}

	/** @test */
	public function it_can_get_the_database_drivers()
	{
		$this->assertCount(4, $this->installer->getDatabaseDrivers());
	}

	/** @test */
	public function it_can_validate_the_user_and_database_data()
	{
		$translator = new \Symfony\Component\Translation\Translator('en', new \Symfony\Component\Translation\MessageSelector);
		$translator->addLoader('array', new \Symfony\Component\Translation\Loader\ArrayLoader);
		$translator->addResource('array', ['validation.required' => ':attribute is required!'], 'en', 'messages');

		$this->app['validator'] = new \Illuminate\Validation\Factory($translator);

		$this->assertCount(3, $this->installer->validate());
	}


	/** @test */
	public function it_can_listen_to_the_before_event()
	{
		$this->app['events']->shouldReceive('listen')->once();

		$this->installer->before(function(){ });
	}

	/** @test */
	public function it_can_listen_to_the_after_event()
	{
		$this->app['events']->shouldReceive('listen')->once();

		$this->installer->after(function(){ });
	}

	/** @test */
	public function it_can_install_platform()
	{
		$this->app           = m::mock('Illuminate\Container\Container[environment]');
		$this->app['events'] = m::mock('Illuminate\Events\Dispatcher');
		$this->app['cache']  = m::mock('Illuminate\Cache\CacheManager');

		$this->app['events']->shouldIgnoreMissing();

		$this->app['cache']->shouldReceive('flush')
			->once();

		$this->installer = new Installer($this->app, $this->repository);

		$this->repository->setDatabaseDriver('mysql');

		$this->app['Illuminate\Foundation\Bootstrap\DetectEnvironment'] = m::mock('stdClass');
		$this->app['Illuminate\Foundation\Bootstrap\DetectEnvironment']->shouldReceive('bootstrap')->once();

		$this->app['Illuminate\Foundation\Bootstrap\LoadConfiguration'] = m::mock('stdClass');
		$this->app['Illuminate\Foundation\Bootstrap\LoadConfiguration']->shouldReceive('bootstrap')->once();

		$this->app['db.factory'] = m::mock('Illuminate\Database\Connectors\ConnectionFactory');
		$this->app['db.factory']->shouldReceive('make')->once();

		$this->app['files'] = m::mock('Illuminate\Filesystem\Filesystem');
		$this->app['files']->shouldReceive('exists')->once()->andReturn(true);
		$this->app['files']->shouldReceive('get')->times(3)->andReturn(['bar' => 'baz']);
		$this->app['files']->shouldReceive('put')->times(3);
		$this->app['files']->shouldReceive('isDirectory')->once()->andReturn(true);

		$this->app['config'] = new \Illuminate\Config\Repository();

		$this->app['db'] = m::mock('Illuminate\Database\DatabaseManager');
		$this->app['db']->shouldReceive('purge')->once();
		$this->app['db']->shouldReceive('setTablePrefix')->once();
		$this->app['db']->shouldReceive('reconnect')->once()->with('mysql');

		$this->app['path'] = __DIR__;
		$this->app['path.base'] = __DIR__;

		$this->app['migration.repository'] = m::mock('Illuminate\Database\Migrations\MigrationRepositoryInterface');
		$this->app['migration.repository']->shouldReceive('createRepository');

		$this->app['migrator'] = m::mock('Illuminate\Database\Migrations\Migrator');
		$this->app['migrator']->shouldReceive('run');

		$this->app['platform'] = m::mock('Platform\Foundation\Platform');
		$this->app['platform']->shouldReceive('getExtensionBag')->andReturn($extensionBag = m::mock('Cartalyst\Extensions\ExtensionBag'));

		$this->app['sentinel'] = m::mock('Cartalyst\Sentinel\Sentinel');
		$this->app['sentinel']->shouldReceive('registerAndActivate')->andReturn($user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'));;
		$this->app['sentinel']->shouldReceive('getRoleRepository')->andReturn($roleRepository = m::mock('Cartalyst\Sentinel\Roles\RoleRepositoryInterface'));

		$user->shouldReceive('roles')->once()->andReturn($rolesRelation = m::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany'));

		$roleRepository->shouldReceive('createModel')->once()->andReturn($role = m::mock('Cartalyst\Sentinel\Roles\EloquentRole'));

		$role->shouldReceive('fill')->once()->andReturn($role);

		$role->shouldReceive('save')->once()->andReturn($role);

		$rolesRelation->shouldReceive('attach')->once();

		$this->app['platform']->shouldReceive('codebaseVersion')->andReturn('1.0.0');

		$extensionBag->shouldReceive('findAndRegisterExtensions')->once();
		$extensionBag->shouldReceive('sortExtensions')->once();

		$extension1 = m::mock('Cartalyst\Extensions\ExtensionInterface');
		$extension1->shouldReceive('install')->once();
		$extension1->shouldReceive('enable')->once();

		$extension2 = m::mock('Cartalyst\Extensions\ExtensionInterface');
		$extension2->shouldReceive('install')->once();
		$extension2->shouldReceive('enable')->once();

		$extensionBag->shouldReceive('all')->once()->andReturn($extensions = [$extension1, $extension2]);

		$this->installer->install();
	}

	/** @test */
	public function it_can_replace_config_string_values()
	{
		$this->installer  = new Installer(
			$this->app        = $this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		);

		$string = <<<STRING
	'foo_bar' => null,
STRING;

		$expected = <<<STRING
	'foo_bar' => '2.0.0',
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo_bar', '2.0.0')
		);

		$string = <<<STRING
	'foo_bar' => null,
STRING;

		$expected = <<<STRING
	'foo_bar' => false,
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo_bar', false)
		);

		$string = <<<STRING
	'foo_bar' => '2.0.0',
STRING;

		$expected = <<<STRING
	'foo_bar' => true,
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo_bar', true)
		);

		// Really test out the regular expression
		$string = <<<STRING
	'foo_bar' => 's0meReallyLongString-@#)($#@%*&*(@#)U@:LK:::K@J#$#@??SF"DS"<> \'\'\'\'\' ',
STRING;

		$expected = <<<STRING
	'foo_bar' => '2.0.0',
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo_bar', '2.0.0')
		);

		// Really test out the regular expression
		$string = <<<STRING
	'. \ + * ? [ ^ ] $ ( ) { } = ! < > | : -' => 'foo',
STRING;

		$expected = <<<STRING
	'. \ + * ? [ ^ ] $ ( ) { } = ! < > | : -' => '. \ + * ? [ ^ ] $ ( ) { } = ! < > | : -',
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, '. \ + * ? [ ^ ] $ ( ) { } = ! < > | : -', '. \ + * ? [ ^ ] $ ( ) { } = ! < > | : -')
		);

		// Now, let's test with different levels of tabbing / spacing
		$string = <<<STRING
	'foo'=>'bar',
STRING;

		$expected = <<<STRING
	'foo' => '2.0.0',
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo', '2.0.0')
		);

		$string = <<<STRING
	'foo'       =>								'bar'     ,
STRING;

		$expected = <<<STRING
	'foo' => '2.0.0',
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'foo', '2.0.0')
		);

		// Very real scenario
		$string = <<<STRING
<?php

return [

	'installed_version' => false,

];
STRING;

		$expected = <<<STRING
<?php

return [

	'installed_version' => '2.0.0',

];
STRING;

		$this->assertEquals(
			$expected,
			$this->installer->replaceConfigStringValue($string, 'installed_version', '2.0.0')
		);
	}

	/** @test */
	public function it_will_write_config_if_it_does_not_exist()
	{
		$this->installer = m::mock('Platform\Installer\Installer[replaceConfigStringValue]', [
			$this->app        = $this->app,
			m::mock('Platform\Installer\Repository')
		]);

		$this->app['Illuminate\Contracts\Console\Kernel'] = m::mock('App\Console\Kernel');
		$this->app['Illuminate\Contracts\Console\Kernel']->shouldReceive('call')->once()->with('vendor:publish', ['--provider' => 'Platform\\Foundation\\Laravel\\PlatformServiceProvider']);

		$this->app['path'] = 'app_path';
		$this->app['path.base'] = '';

		$this->app['files'] = $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
		$filesystem->shouldReceive('isDirectory')->with($expectedConfigPath = '/config')->once()->andReturn(false);
		$filesystem->shouldReceive('exists')->with($expectedConfigFile = $expectedConfigPath.'/platform-foundation.php')->never();

		$filesystem->shouldReceive('get')->with($expectedConfigFile)->once()->andReturn('success');
		$this->installer->shouldReceive('replaceConfigStringValue')->with('success', 'installed_version', '2.0.0')->once()->andReturn('woohoo');

		$filesystem->shouldReceive('put')->with($expectedConfigFile, 'woohoo')->once()->andReturn(true);

		$this->installer->updatePlatformInstalledVersion('2.0.0');
	}

	/**
	 * @test
	 * @expectedException RuntimeException
	 */
	public function test_updating_installed_version_when_config_does_not_exist_throws_error()
	{
		$this->installer = m::mock('Platform\Installer\Installer[replaceConfigStringValue]', [
			$this->app        = $this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		]);

		$this->app['Illuminate\Contracts\Console\Kernel'] = m::mock('App\Console\Kernel');
		$this->app['Illuminate\Contracts\Console\Kernel']->shouldReceive('call')->once()->with('vendor:publish', ['--provider' => 'Platform\\Foundation\\Laravel\\PlatformServiceProvider']);

		$this->app['path'] = 'app_path';
		$this->app['path.base'] = '';

		// Firstly, the function should check for the published config
		$this->app['files'] = $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
		$filesystem->shouldReceive('isDirectory')->with($expectedConfigPath = '/config')->once()->andReturn(false);
		$filesystem->shouldReceive('exists')->with($expectedConfigFile = $expectedConfigPath.'/platform-foundation.php')->never();

		// THen the installer updates the config value
		$filesystem->shouldReceive('get')->with($expectedConfigFile)->once()->andReturn('success');
		$this->installer->shouldReceive('replaceConfigStringValue')->with('success', 'installed_version', '2.0.0')->once()->andReturn('woohoo');

		// This should trigger an error to output
		$filesystem->shouldReceive('put')->with($expectedConfigFile, 'woohoo')->once()->andReturn(false);

		$this->installer->updatePlatformInstalledVersion('2.0.0');
	}

	/** @test */
	public function test_updating_installed_version_does_not_overwrite_existing_config()
	{
		$this->installer = m::mock('Platform\Installer\Installer[replaceConfigStringValue]', [
			$this->app        = $this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		]);

		$this->app['path'] = 'app_path';
		$this->app['path.base'] = '';

		// Firstly, the function should check for the published config
		$this->app['files'] = $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
		$filesystem->shouldReceive('isDirectory')->with($expectedConfigPath = '/config')->once()->andReturn(true);
		$filesystem->shouldReceive('exists')->with($expectedConfigFile = $expectedConfigPath.'/platform-foundation.php')->once()->andReturn(true);

		// Then the installer updates the config value
		$filesystem->shouldReceive('get')->with($expectedConfigFile)->once()->andReturn('success');
		$this->installer->shouldReceive('replaceConfigStringValue')->with('success', 'installed_version', '2.0.0')->once()->andReturn('woohoo');

		// This should trigger an error to output
		$filesystem->shouldReceive('put')->with($expectedConfigFile, 'woohoo')->once()->andReturn(true);

		$this->installer->updatePlatformInstalledVersion('2.0.0');
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function test_setting_database_does_not_catch_exceptions1()
	{
		$this->app['events']->shouldIgnoreMissing();

		$this->installer = m::mock('Platform\Installer\Installer[createDatabaseConfig]', [
			$this->app        = $this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		]);

		$this->repository->shouldReceive('getDatabaseData')->andReturn(['driver'=>'foo',]);
		$this->repository->shouldReceive('getDatabaseDriver')->andReturn('foo');
		$this->repository->shouldReceive('getDatabaseConfig')->andReturn(['bar' => 'baz']);

		$this->app['db.factory'] = $dbFactory = m::mock('Illuminate\Database\Connectors\ConnectionFactory');
		$dbFactory->shouldReceive('make')->with([
			'driver' => 'foo',
		])->once()->andThrow(new \InvalidArgumentException);

		$this->installer->install();
	}

	/**
	 * @test
	 * @expectedException PDOException
	 */
	public function test_setting_database_does_not_catch_exceptions2()
	{
		$this->app['events']->shouldIgnoreMissing();

		$this->installer = m::mock('Platform\Installer\Installer[createDatabaseConfig]', [
			$this->app        = $this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		]);

		$this->repository->shouldReceive('getDatabaseData')->andReturn(['driver'=>'foo',]);
		$this->repository->shouldReceive('getDatabaseDriver')->andReturn('foo');
		$this->repository->shouldReceive('getDatabaseConfig')->andReturn(['bar' => 'baz']);

		$this->app['db.factory'] = $dbFactory = m::mock('Illuminate\Database\Connectors\ConnectionFactory');
		$dbFactory->shouldReceive('make')->with([
			'driver' => 'foo',
		])->once()->andThrow(new \PDOException);

		$this->installer->install();
	}

	/**
	 * @test
	 * @expectedException RuntimeException
	 */
	public function test_creating_database_config_throws_exception_if_config_cannot_be_written()
	{
		$this->app           = m::mock('Illuminate\Container\Container[environment]');
		$this->app['events'] = m::mock('Illuminate\Events\Dispatcher');

		$this->app['events']->shouldIgnoreMissing();

		$this->installer = m::mock('Platform\Installer\Installer[createDatabaseConfig]', [
			$this->app,
			$this->repository = m::mock('Platform\Installer\Repository')
		]);

		$this->repository->shouldReceive('getDatabaseData')->andReturn(['driver'=>'foo',]);
		$this->repository->shouldReceive('getDatabaseDriver')->andReturn('foo');
		$this->repository->shouldReceive('getDatabaseConfig')->andReturn(['bar' => 'baz']);

		$this->app['db.factory'] = $dbFactory = m::mock('Illuminate\Database\Connectors\ConnectionFactory');
		$dbFactory->shouldReceive('make')->with([
			'driver' => 'foo',
		])->once();

		$configFileRegex = '/stubs\/database\/foo\.php$/';

		$this->repository->shouldReceive('getDatabaseDriver')->andReturn('foo');
		$this->repository->shouldReceive('getDatabaseConfig')->andReturn(['database' => 'bar']);

		$this->app['files'] = $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
		$filesystem->shouldReceive('get')->with($configFileRegex)->andReturn();

		$this->app['path'] = __DIR__;
		$this->app['path.base'] = '';

		// This should trigger an Exception
		$filesystem->shouldReceive('put')->andReturn(false);

		$this->installer->install();
	}

}
