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

interface RequirementInterface {

	/**
	 * Performs the requirement check.
	 *
	 * @return bool
	 */
	public function check();

	/**
	 * Returns the title translation key.
	 *
	 * @return string
	 */
	public function title();

	/**
	 * Returns the message translation key.
	 *
	 * @return string
	 */
	public function message();

}
