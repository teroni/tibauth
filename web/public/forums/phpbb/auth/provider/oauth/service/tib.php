<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace phpbb\auth\provider\oauth\service;

/**
* Bitly OAuth service
*/
class tib extends \phpbb\auth\provider\oauth\service\base
{
	/**
	* phpBB config
	*
	* @var \phpbb\config\config
	*/
	protected $config;

	/**
	* phpBB request
	*
	* @var \phpbb\request\request_interface
	*/
	protected $request;

	/**
	* Constructor
	*
	* @param	\phpbb\config\config				$config
	* @param	\phpbb\request\request_interface 	$request
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request)
	{

		$this->config = $config;
		$this->request = $request;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_auth_scope()
	{
		return array(
			'first_name',
			'user_id',
		);
	}

	/**
	* {@inheritdoc}
	*/
	public function get_service_credentials()
	{
		return array(
			'key'		=> $this->config['auth_oauth_tib_key'],
			'secret'	=> $this->config['auth_oauth_tib_secret'],
		);
	}

	/**
	* {@inheritdoc}
	*/
	public function perform_auth_login()
	{

		// This was a callback request, get the token
		$token = $this->service_provider->requestAccessToken($this->request->variable('code', ''));
		$data = $this->service_provider->requestUserData($token);
		// Send a request with it
		//$result = json_decode($this->service_provider->request('http://10.0.1.11/phpbb/resource'), true);
		// Return the unique identifier
		return $data;
	}

	/**
	* {@inheritdoc}
	*/
	public function perform_token_auth()
	{
		echo "test";
		die();
		if (!($this->service_provider instanceof \OAuth\OAuth2\Service\Tibauth))
		{
			throw new exception('AUTH_PROVIDER_OAUTH_ERROR_INVALID_SERVICE_TYPE');
		}

		// Send a request with it
		$result = json_decode($this->service_provider->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

		// Return the unique identifier
		return $result['id'];
	}
}
