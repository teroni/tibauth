<?php namespace Tib\Providers;

use \League\OAuth2\Client\Entity\User;

class Ccpsso extends \League\OAuth2\Client\Provider\AbstractProvider {
	    public function __construct($options)
    {
        parent::__construct($options);
        $this->headers = [
            'Authorization' => 'Bearer ',
        ];
        $this->token = '';
    }

// Default scopes
public $scopes = array('scope1', 'scope2');

// Response type
public $responseType = 'json';
public function test()
{
	return "ok";
}
public function urlAuthorize()
{
return 'https://login.eveonline.com/oauth/authorize/';
}

public function urlAccessToken()
{
return 'https://login.eveonline.com/oauth/token';
}

public function getAccessToken($grant = 'authorization_code', $params = [])
    {
        if (is_string($grant)) {
            // PascalCase the grant. E.g: 'authorization_code' becomes 'AuthorizationCode'
            $className = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $grant)));
            $grant = 'League\\OAuth2\\Client\\Grant\\'.$className;
            if (! class_exists($grant)) {
                throw new \InvalidArgumentException('Unknown grant "'.$grant.'"');
            }
            $grant = new $grant();
        } elseif (! $grant instanceof GrantInterface) {
            $message = get_class($grant).' is not an instance of League\OAuth2\Client\Grant\GrantInterface';
            throw new \InvalidArgumentException($message);
        }

        $defaultParams = [
        	'Authorization' => 'Basic '. base64_encode($this->clientId. ':'. $this->clientSecret),
        	'Content-Type'	=> 'application/x-www-form-urlencoded',
        	'Host'			=> 'login.eveonline.com',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
        ];
        $this->method = "POST";

        $requestParams = $grant->prepRequestParams($defaultParams, $params);

        try {
            switch (strtoupper($this->method)) {
                case 'GET':
                    // @codeCoverageIgnoreStart
                    // No providers included with this library use get but 3rd parties may
                    $client = $this->getHttpClient();
                    $client->setBaseUrl($this->urlAccessToken() . '?' . $this->httpBuildQuery($requestParams, '', '&'));
                    $request = $client->get(null, null, $requestParams)->send();
                    $response = $request->getBody();
                    break;
                    // @codeCoverageIgnoreEnd
                case 'POST':
                    $client = $this->getHttpClient();
                    $client->setBaseUrl($this->urlAccessToken());
                    $request = $client->post(null, null, $requestParams)->send();
                    $response = $request->getBody();
                    break;
                // @codeCoverageIgnoreStart
                default:
                    throw new \InvalidArgumentException('Neither GET nor POST is specified for request');
                // @codeCoverageIgnoreEnd
            }
        } catch (BadResponseException $e) {
            // @codeCoverageIgnoreStart
            $response = $e->getResponse()->getBody();
            // @codeCoverageIgnoreEnd
        }

        switch ($this->responseType) {
            case 'json':
                $result = json_decode($response, true);
                break;
            case 'string':
                parse_str($response, $result);
                break;
        }

        if (isset($result['error']) && ! empty($result['error'])) {
            // @codeCoverageIgnoreStart
            throw new IDPException($result);
            // @codeCoverageIgnoreEnd
        }

        $result = $this->prepareAccessTokenResult($result);

        return $grant->handleResponse($result);
    }

public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
{
return 'https://login.eveonline.com/oauth/verify';
}
protected function fetchUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        $url = $this->urlUserDetails($token);
        $this->token = $token;

        return $this->fetchProviderData($url);
    }
protected function fetchProviderData($url, array $headers = [])
    {
        try {
            $client = $this->getHttpClient();
            $client->setBaseUrl($url);
            $this->headers = [
	        	'Authorization' => 'Bearer '. $this->token,
	        	'Host'			=> 'login.eveonline.com',
	        ];
            if ($this->headers) {
                $client->setDefaultOption('headers', $this->headers);
            }
            
            $request = $client->get()->send();
            $response = $request->getBody();
        } catch (BadResponseException $e) {
            // @codeCoverageIgnoreStart
            $raw_response = explode("\n", $e->getResponse());
            throw new IDPException(end($raw_response));
            // @codeCoverageIgnoreEnd
        }

        return $response;
    }

public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token)
{
$user = new User;

// Take the decoded data (determined by $this->responseType)
// and fill out the user object by abstracting out the API
// properties (this keeps our user object simple and adds
// a layer of protection in-case the API response changes)

$user->name = $response->CharacterName;
$user->uid = $response->CharacterID;
$user->email      = $response->CharacterName. '@eve.com';

return $user;
}

public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
{
	//var_dump($response);
	print_r($response);
	return $response->CharacterID;
}

public function userEmail($response, \League\OAuth2\Client\Token\AccessToken $token)
{
// Optional, however OAuth2 usually provides a scope
// to receive access to a user's email, you should always
// ask for this scope, as having an email is awesome.
if (isset($response->CharacterName))
{
return $response->CharacterName. '@eve.com';
}
}

public function userScreenName($response, \League\OAuth2\Client\Token\AccessToken $token)
{
// Optional
if (isset($response->CharacterName))
{
return $response->CharacterName;
}
}
}