<?php namespace Platform\Installer;
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

use Closure;
use Illuminate\Support\Str;
use Cartalyst\Extensions\Extension;
use Illuminate\Container\Container;

class Installer {

	/**
	 * The Illuminate container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $laravel;

	/**
	 * The installer repository instance.
	 *
	 * @var \Platform\Installer\Repository
	 */
	protected $repository;

	/**
	 * List of required packages.
	 *
	 * @var array
	 */
	protected $requiredPackages = [
		'cartalyst/extensions',
		'cartalyst/sentinel',
		'platform/foundation',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $laravel
	 * @param  \Platform\Installer\Repository  $repository
	 * @return void
	 */
	public function __construct(Container $laravel, Repository $repository)
	{
		$this->laravel = $laravel;

		$this->repository = $repository;
	}

	/**
	 * Returns the installer repository.
	 *
	 * @return \Platform\Installer\Repository
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * Returns the installer repository.
	 *
	 * @param  \Platform\Installer\Repository  $repository
	 * @return $this
	 */
	public function setRepository(Repository $repository)
	{
		$this->repository = $repository;

		return $this;
	}

	/**
	 * Returns the user config data.
	 *
	 * @return array
	 */
	public function getUserData()
	{
		return $this->repository->getUserData();
	}

	/**
	 * Sets the user config data.
	 *
	 * @param  array  $data
	 * @return $this
	 */
	public function setUserData(array $data = [])
	{
		$this->repository->setUserData($data);

		return $this;
	}

	/**
	 * Returns the database config data for the given driver.
	 *
	 * @param  string  $driver
	 * @return array
	 */
	public function getDatabaseData($driver = null)
	{
		return $this->repository->getDatabaseData($driver);
	}

	/**
	 * Sets the database config data for the given driver.
	 *
	 * @param  string  $driver
	 * @param  array  $data
	 * @return $this
	 */
	public function setDatabaseData($driver, array $data = [])
	{
		$this->repository->setDatabaseData($driver, $data);

		return $this;
	}

	/**
	 * Returns all the available database drivers.
	 *
	 * @return array
	 */
	public function getDatabaseDrivers()
	{
		$drivers = [];

		foreach ($this->getDatabaseData() as $driver => $fields)
		{
			$rules = $this->repository->getDatabaseRules($driver);

			foreach ($fields as $field => $value)
			{
				$drivers[$driver][$field] = [
					'value' => $value,
					'rules' => array_get($rules, $field),
				];
			}
		}

		return $drivers;
	}

	/**
	 * Validates the given data against the given rules.
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validate()
	{
		$validator = $this->laravel['validator']->make(
			$this->prepareValidationData(), $this->prepareValidationRules()
		);

		return $validator->errors();
	}

	/**
	 * Prepares the data to be validated.
	 *
	 * @return array
	 */
	protected function prepareValidationData()
	{
		$driver = $this->repository->getDatabaseDriver();

		$databaseData = [];

		foreach ($this->repository->getDatabaseData($driver) as $field => $value)
		{
			$databaseData["{$driver}.{$field}"] = $value;
		}

		return array_merge($this->repository->getUserData(), $databaseData);
	}

	/**
	 * Prepares the validation rules.
	 *
	 * @return array
	 */
	protected function prepareValidationRules()
	{
		$driver = $this->repository->getDatabaseDriver();

		$databaseRules = [];

		foreach ($this->repository->getDatabaseRules($driver) as $field => $rule)
		{
			$databaseRules["{$driver}.{$field}"] = $rule;
		}

		return array_merge($this->repository->getUserRules(), $databaseRules);
	}

	/**
	 * Installs Platform.
	 *
	 * @param  bool  $testing
	 * @return void
	 */
	public function install($testing = false)
	{
		// Fire the "installer.before" event
		$this->laravel['events']->fire('installer.before');

		if ( ! $testing)
		{
			// Install config
			$this->setupDatabase();
		}

		// Migrate the required packages
		$this->migrateRequiredPackages();

		// Install platform extensions
		$this->installExtensions();

		if ( ! $testing)
		{
			// Disable checkpoints
			unset($this->laravel['sentinel.checkpoints']);

			// Create default user
			$this->createDefaultUser();
		}

		// Fire the "installer.after" event
		$this->laravel['events']->fire('installer.after');

		if ( ! $testing)
		{
			// Write platform's installed version config
			$this->updatePlatformInstalledVersion($this->laravel['platform']->codebaseVersion());
		}
	}

	/**
	 * Sets up the database to work with Platform from the given repository.
	 *
	 * @return void
	 */
	protected function setupDatabase()
	{
		$driver = $this->repository->getDatabaseDriver();

		$config = $this->repository->getDatabaseData($driver);

		try
		{
			$this->laravel['db.factory']->make(array_merge(compact('driver'), $config));
		}
		catch (\PDOException $e)
		{
			throw $e;
		}

		$configFile = __DIR__."/stubs/database/{$driver}.php";
		$envStub = __DIR__.'/stubs/env.stub';

		// Now, let's update our stub file with
		// our actual database credentials
		$contents = str_replace(
			array_map(function($key)
			{
				return '{{'.$key.'}}';
			}, array_keys($config)),
			array_values($config),
			$this->laravel['files']->get($envStub)
		);

		// Generate a key
		$contents = str_replace('{{key}}', Str::random(32), $contents);

		// Just a triple check we can actually
		// write the configuration.
		if ($this->laravel['files']->put(($envFile = $this->laravel['path.base'].'/.env'), $contents) === false)
		{
			throw new \RuntimeException("Could not write env file to [$envFile].");
		}

		if ($this->laravel['files']->put(($newConfigFile = $this->laravel['path.base'].'/config/database.php'), $this->laravel['files']->get($configFile)) === false)
		{
			throw new \RuntimeException("Could not write database config file to [$newConfigFile].");
		}

		// Reload env file
		$this->laravel['Illuminate\Foundation\Bootstrap\DetectEnvironment']->bootstrap($this->laravel);

		// Reload config
		$this->laravel['Illuminate\Foundation\Bootstrap\LoadConfiguration']->bootstrap($this->laravel);

		// Purge old connection
		$this->laravel['db']->purge();

		// Set table prefix on the connection
		$this->laravel['db']->setTablePrefix(array_get($config, 'prefix'));

		// Reconnect using the new config
		$this->laravel['db']->reconnect($driver);
	}

	/**
	 * Ensures we're ready to run the migrations.
	 *
	 * @return void
	 */
	protected function prepareMigrationRepository()
	{
		try
		{
			$this->laravel['migration.repository']->getLast();
		}
		catch (\Exception $e)
		{
			$this->laravel['migration.repository']->createRepository();
		}
	}

	/**
	 * Migrates all the required packages for Platform.
	 *
	 * @return void
	 */
	protected function migrateRequiredPackages()
	{
		$this->prepareMigrationRepository();

		$path = $this->laravel['path.base'];

		foreach ($this->requiredPackages as $package)
		{
			$this->laravel['migrator']->run(
				"{$path}/vendor/{$package}/src/migrations"
			);
		}
	}

	/**
	 * Installs all the available extensions.
	 *
	 * @return void
	 */
	protected function installExtensions()
	{
		// Alright, database connection established. Let's now grab all
		// core extensions
		$extensionBag = $this->laravel['platform']->getExtensionBag();

		// Register all local extension
		$extensionBag->findAndRegisterExtensions();

		// Sort the extensions by their dependencies
		$extensionBag->sortExtensions();

		// Set the connection resolver on our extensions
		Extension::setConnectionResolver($this->laravel['db']);

		// Set the laravel migrator on our extensions
		Extension::setMigrator($this->laravel['migrator']);

		// Flush the Cache
		$this->laravel['cache']->flush();

		// Loop through extensions
		foreach ($extensionBag->all() as $extension)
		{
			$extension->install();

			$extension->enable();
		}
	}

	/**
	 * Creates the default user into Platform (including creating
	 * necessary roles).
	 *
	 * @return void
	 */
	protected function createDefaultUser()
	{
		// Get the user configuration data
		$config = $this->getUserData();

		// Get the Sentinel instance
		$sentinel = $this->laravel['sentinel'];

		// Create the admin role
		$role = $sentinel->getRoleRepository()->createModel();
		$role->fill([
			'slug'        => 'admin',
			'name'        => 'Admin',
			'permissions' => [
				'superuser' => true,
			],
		]);
		$role->save();

		// Create the user
		$user = $sentinel->registerAndActivate([
			'email'    => $config['email'],
			'password' => $config['password'],
		]);

		// Attach the admin role to the user
		$user->roles()->attach($role);
	}






	/**
	 * Event listener for the 'installer.before' event.
	 *
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function before(Closure $callback)
	{
		$this->laravel['events']->listen('installer.before', $callback);
	}

	/**
	 * Event listener for the 'installer.after' event.
	 *
	 * @param  \Closure  $callback
	 * @param  int  $priority
	 * @return void
	 */
	public function after(Closure $callback, $priority = 0)
	{
		$this->laravel['events']->listen('installer.after', $callback, $priority);
	}


	/**
	 * Updates the Platform installed version in the configuration
	 * file to match that of which is actually installed, or a particular
	 * version.
	 *
	 * @param  string  $version
	 * @return void
	 */
	public function updatePlatformInstalledVersion($version)
	{
		// Let's check if we've been published or not
		$configPath = $this->laravel['path.base'].'/config';
		$configFile = $configPath.'/platform-foundation.php';

		// If we haven't published our config
		if ( ! $this->laravel['files']->isDirectory($configPath) or ! $this->laravel['files']->exists($configFile))
		{
			$this->laravel['Illuminate\Contracts\Console\Kernel']->call('vendor:publish', ['--provider' => 'Platform\\Foundation\\Laravel\\PlatformServiceProvider']);
		}

		// Let's replace the 'installed_version' property
		// with the actual installed version.
		$contents = $this->replaceConfigStringValue($this->laravel['files']->get($configFile), 'installed_version', $version);

		// Just a triple check we can actually
		// write the configuration.
		if ($this->laravel['files']->put($configFile, $contents) === false)
		{
			throw new \RuntimeException("Could not write Platform config file to [$configFile].");
		}
	}

	/**
	 * Replaces a config string value.
	 *
	 * @param  string  $string
	 * @param  string  $key
	 * @param  string  $value
	 * @return string
	 */
	public function replaceConfigStringValue($string, $key, $value)
	{
		// If we have null, true, false etc
		$reserved = ['null', 'true', 'false'];

		// When we do our replacements, we don't want to
		// wrap keywords in values. All others, we'll add
		// slashes to escape quotes
		if (in_array($_value = strtolower(var_export($value, true)), $reserved))
		{
			$value = $_value;
		}
		else
		{
			$value = '\''.addslashes($value).'\'';
		}

		return preg_replace(
			sprintf(
				'/\'(%s)\'(?:\s+|\t+)?\=\>(?:\s+|\t+)?(.*)(?!\r|,)/',
				preg_quote($key)
			),
			sprintf(
				'\'$1\' => %s,',
				$value
			),
			$string
		);
	}

}
