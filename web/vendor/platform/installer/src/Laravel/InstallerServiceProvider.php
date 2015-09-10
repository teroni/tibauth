<?php namespace Platform\Installer\Laravel;
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

use Platform\Installer\Installer;
use Platform\Installer\Repository;
use Illuminate\Support\ServiceProvider;
use Platform\Installer\Console\InstallCommand;

class InstallerServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->registerRoutes();

		$this->observeEvents();

		$this->app['platform']->addEligibilityWhitelist('installer');

		if ( ! $this->app['platform']->isInstalled())
		{
			$active = $this->app['config']->get('cartalyst.themes.config.active') ?: 'frontend::default';

			$this->app['themes']->setActive($active);
		}

		$this->loadViews();
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerMiddleware();

		$this->prepareResources();

		$this->registerRepository();

		$this->registerInstaller();

		$this->registerCommands();
	}

	/**
	 * Loads the views.
	 *
	 * @return void
	 */
	protected function loadViews()
	{
		$views = realpath(__DIR__.'/../../views');

		$this->loadViewsFrom($views, 'platform/installer');
	}

	/**
	 * Prepends the installer middleware.
	 *
	 * @return void
	 */
	protected function registerMiddleware()
	{
		$this->app['Illuminate\Contracts\Http\Kernel']->prependMiddleware('Platform\Installer\Middleware\Installer');
	}

	/**
	 * Prepare the package resources.
	 *
	 * @return void
	 */
	protected function prepareResources()
	{
		$assets = realpath(__DIR__.'/../../public');;

		$this->publishes([
			$assets => public_path('packages/platform/installer'),
		], 'assets');
	}


	/**
	 * Register the installer routes.
	 *
	 * @return void
	 */
	protected function registerRoutes()
	{
		$this->app['router']->group([
			'prefix'    => 'installer',
			'namespace' => 'Platform\Installer\Controllers'
		], function($router)
		{
			$router->get('/'       , 'InstallerController@index');
			$router->post('/'      , 'InstallerController@configure');
			$router->get('complete', 'InstallerController@complete');
		});
	}

	/**
	 * Register some event listeners.
	 *
	 * @return void
	 */
	protected function observeEvents()
	{
		$this->app['events']->listen('artisan.start', function($artisan)
		{
			if ( ! $this->app['platform']->isInstalled())
			{
				$artisan->resolveCommands(['command.platform.install']);
			}
		});
	}

	/**
	 * Registers the installer repository which is used when installing Platform.
	 *
	 * @return void
	 */
	protected function registerRepository()
	{
		$this->app['platform.installer.repository'] = $this->app->share(function()
		{
			return new Repository;
		});

		$this->app->alias('platform.installer.repository', 'Platform\Installer\Repository');
	}

	/**
	 * Registers the installer.
	 *
	 * @return void
	 */
	protected function registerInstaller()
	{
		$this->app['platform.installer'] = $this->app->share(function($app)
		{
			return new Installer($app, $app['platform.installer.repository']);
		});

		$this->app->alias('platform.installer', 'Platform\Installer\Installer');
	}

	/**
	 * Registers the install command.
	 *
	 * @return void
	 */
	protected function registerCommands()
	{
		$this->app['command.platform.install'] = $this->app->share(function($app)
		{
			return new InstallCommand($app['platform.installer']);
		});
	}

}
