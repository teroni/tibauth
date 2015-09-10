<?php namespace Platform\Installer\Requirements;
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

class ConfigPermissionsRequirement implements RequirementInterface {

	/**
	 * {@inheritDoc}
	 */
	public function check()
	{
		return is_writable(realpath(__DIR__.'/../app/config'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function title()
	{
		return 'Public Write Permissions';
	}

	/**
	 * {@inheritDoc}
	 */
	public function message()
	{
		return 'app/config must be writable.';
	}

}
