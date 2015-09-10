<?php namespace Platform\Installer\Controllers;
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

use Platform\Foundation\Platform;
use Platform\Installer\Installer;
use Illuminate\Routing\Controller;
use Platform\Installer\Requirements;
use Illuminate\Filesystem\Filesystem;

class InstallerController extends Controller {

	/**
	 * The platform instance.
	 *
	 * @var \Platform\Foundation\Platform
	 */
	protected $platform;

	/**
	 * The installer instance.
	 *
	 * @var \Platform\Installer\Installer
	 */
	protected $installer;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Foundation\Platform  $platform
	 * @param  \Platform\Installer\Installer  $installer
	 * @param  \Illuminate\Filesystem\Filesystem  $filesystem
	 * @return void
	 */
	public function __construct(Platform $platform, Installer $installer, Filesystem $filesystem)
	{
		$this->platform = $platform;

		$this->installer = $installer;

		$this->filesystem = $filesystem;

		$this->beforeFilter(function($route, $request)
		{
			$isInstalled = $this->platform->isInstalled();

			$completionStep = ends_with($request->path(), 'complete');

			if ( ! $completionStep && $isInstalled) return redirect('/');

			if ($completionStep && ! $isInstalled) return redirect('installer');
		});
	}

	/**
	 * Shows the "configure" screen.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		// Get the database drivers
		$drivers = $this->installer->getDatabaseDrivers();

		$requirements = [
			new Requirements\ConfigPermissionsRequirement,
		];

		$pass = true;

		foreach ($requirements as $requirement)
		{
			if ( ! $requirement->check())
			{
				$pass = false;

				break;
			}
		}

		return view('platform/installer::configure', compact('drivers', 'requirements', 'pass'));
	}

	/**
	 * Handles the configuration and installation of Platform.
	 *
	 * @return mixed
	 */
	public function configure()
	{
		$this->installer->setUserData(
			request()->input('user', [])
		);

		$this->installer->setDatabaseData(
			$driver = request()->input('database.driver'),
			request()->input("database.{$driver}", [])
		);

		$messages = $this->installer->validate();

		if ( ! $messages->isEmpty())
		{
			return redirect()->back()->withLicense(true)->withInput()->withErrors($messages);
		}

		try
		{
			$this->installer->install();
		}
		catch (\Exception $e)
		{
			return redirect()->back()->withLicense(true)->withInput()->withErrors($e->getMessage());
		}

		return redirect('installer/complete');
	}

	/**
	 * Shows the "complete" screen.
	 *
	 * @return \Illuminate\View\View
	 */
	public function complete()
	{
		return view('platform/installer::complete');
	}

}
