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

use PHPUnit_Framework_TestCase;
use Platform\Installer\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * The installer repository instance.
	 *
	 * @var \Platform\Installer\Repository
	 */
	protected $repository;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->repository = new Repository;
	}

	/** @test */
	public function it_can_set_the_user_data()
	{
		$this->repository->setUserData([
			'email'            => 'user@example.com',
			'password'         => 'secret',
			'password_confirm' => 'secret',
		]);

		$this->assertNotEmpty(array_filter($this->repository->getUserData()));
	}

	/** @test */
	public function it_can_get_the_user_rules()
	{
		$this->assertCount(3, $this->repository->getUserRules());
	}

	/** @test */
	public function it_can_set_the_database_data()
	{
		$this->repository->setDatabaseData('mysql', [
			'database' => 'foobar',
			'username' => 'foo',
			'password' => 'bar',
		]);

		$this->assertCount(4, $this->repository->getDatabaseData());

		$this->assertCount(7, $this->repository->getDatabaseData('mysql'));

		$this->assertEquals('mysql', $this->repository->getDatabaseDriver());
	}

	/**
	 * @test
	 * @expectedException RuntimeException
	 */
	public function it_throws_an_exception_when_setting_an_invalid_database_driver()
	{
		$this->repository->setDatabaseDriver('foo');
	}

	/** @test */
	public function it_can_get_the_database_rules()
	{
		$this->assertCount(4, $this->repository->getDatabaseRules());

		$this->assertCount(5, $this->repository->getDatabaseRules('mysql'));
	}

}
