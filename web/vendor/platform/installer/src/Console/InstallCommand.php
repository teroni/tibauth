<?php namespace Platform\Installer\Console;
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

use Illuminate\Console\Command;
use Platform\Installer\Installer;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'platform:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install Platform';

	/**
	 * The installer assosiated with the command.
	 *
	 * @var \Platform\Installer\Installer
	 */
	protected $installer;

	/**
	 * Create a new install command instance
	 *
	 * @param  \Platform\Installer\Installer  $installer
	 * @return void
	 */
	public function __construct(Installer $installer)
	{
		parent::__construct();

		$this->installer  = $installer;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Show the welcome message
		$this->showWelcomeMessage();

		// Ask for the database details
		$this->askDatabaseDetails();

		// Ask for the default user details
		$this->askDefaultUserDetails();

		// Install Platform :)
		$this->installer->install();

		$this->comment('Installation complete :)');
	}

	/**
	 * Shows the welcome message.
	 *
	 * @return void
	 */
	protected function showWelcomeMessage()
	{
		$this->output->writeln(<<<WELCOME
<fg=white>
*-----------------------------------------------*
|                                               |
|       Welcome to the Platform Installer       |
|            Copyright (c) 2011-2014            |
|                 Cartalyst LLC.                |
|                                               |
|         Platform is released under the        |
|                 Cartalyst PSL                 |
|         https://cartalyst.com/license         |
|          Thanks for using Platform!           |
|                                               |
*-----------------------------------------------*
</fg=white>
WELCOME
		);
	}

	/**
	 * Prompts the user for the database driver.
	 *
	 * @param  array  $drivers
	 * @return string
	 */
	protected function askDatabaseDriver(array $drivers)
	{
		// Get only the database drivers keys
		$drivers = array_keys($drivers);

		// Ask for the database driver
		$message = sprintf('<fg=green>Please enter the database</fg=green> [driver] (available: %s): ', implode(', ', $drivers));

		return $this->askQuestion($message, null, false, function($answer) use ($drivers)
		{
			if (in_array($answer, $drivers)) return $answer;

			if ( ! $answer)
			{
				throw new \RuntimeException('Please enter one of the given drivers!');
			}

			throw new \RuntimeException("Driver '{$answer}' is not a valid driver! Please try again.");
		});
	}

	/**
	 * Prompts the user for the database credentials.
	 *
	 * @return void
	 */
	protected function askDatabaseDetails()
	{
		$this->output->writeln(<<<STEP
<fg=yellow>
*-----------------------------------------------*
|                                               |
|         Step #1: Configure Database           |
|                                               |
*-----------------------------------------------*
</fg=yellow>
STEP
		);

		// Get all the available database drivers
		$drivers = $this->installer->getDatabaseDrivers();

		// Ask the user to select the database driver
		$driver = $this->askDatabaseDriver($drivers);

		// Hold the database details
		$databaseData = [];

		// Loop through the selected driver fields
		foreach ($drivers[$driver] as $field => $config)
		{
			// Prepare the field name to avoid confusion
			$fieldName = $field === 'database' ? 'name' : $field;

			// Determine if the field output should be hidden
			$isHidden = ($field === 'password');

			// Prepare the question message
			if ($value = $config['value'])
			{
				$question = sprintf(
					'<fg=green>Please enter the database</fg=green> [%s] (enter for [%s]): ',
					$fieldName,
					$config['value']
				);
			}
			else
			{
				$question = sprintf(
					'<fg=green>Please enter the database</fg=green> [%s]: ',
					$fieldName
				);
			}

			// Prepare the question validator
			$validator = function($answer) use ($fieldName, $config)
			{
				if ( ! $answer && $config['rules'] === 'required')
				{
					throw new \RuntimeException("The database '{$fieldName}' field is required!");
				}

				return $answer;
			};

			// Ask the question to the user and store the answer
			$databaseData[$field] = $this->askQuestion($question, $config['value'], $isHidden, $validator);
		}

		$this->installer->setDatabaseData($driver, $databaseData);
	}

	/**
	 * Prompts the user for the user credentials.
	 *
	 * @return void
	 */
	protected function askDefaultUserDetails()
	{
		$this->output->writeln(<<<STEP
<fg=yellow>
*-----------------------------------------------*
|                                               |
|       Step #2: Configure Default User         |
|                                               |
*-----------------------------------------------*
</fg=yellow>
STEP
		);

		$userData = [];

		$userData['email'] = $this->askQuestion('<fg=green>Please enter the user email</fg=green>: ', null, false, function($answer)
		{
			if ( ! $answer) throw new \RuntimeException('The email is required!');

			return $answer;
		});

		$userData['password'] = $this->askQuestion('<fg=green>Please enter the user password</fg=green>: ', null, true, function($answer)
		{
			if ( ! $answer) throw new \RuntimeException('The password is required!');

			return $answer;
		});

		$this->askQuestion('<fg=green>Please confirm the user password</fg=green>: ', null, true, function($answer) use ($userData)
		{
			if ( ! $answer) throw new \RuntimeException('The password confirmation is required!');

			if ($answer !== $userData['password']) throw new \RuntimeException('The passwords doesn\'t match!');

			return $answer;
		});

		$this->installer->setUserData($userData);
	}

	/**
	 * Asks the user the given question.
	 *
	 * @param  string  $question
	 * @param  mixed  $default
	 * @param  bool  $hidden
	 * @param  \Closure  $validator
	 * @return string
	 */
	protected function askQuestion($question, $default = null, $hidden = false, \Closure $validator = null)
	{
		$q = new Question($question, $default);

		$q->setValidator($validator);

		if ($hidden === true)
		{
			$q->setHidden(true);

			$q->setHiddenFallback(false);
		}

		return $this->getHelper('question')->ask($this->input, $this->output, $q);
	}

}
