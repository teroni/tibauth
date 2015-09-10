<?php namespace Tib\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Sentinel;
use Illuminate\Http\Request;
use \Redirect;
use \Input;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class phpBBController extends Controller {
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function getLogin($apikey) {
        try {
            if ($apikey === Config::get('bridgeapi.bridgebb-apikey')) {
                return $this->_validateCredentials();
            } else {
                throw new Exception('Invalid API Key');
            }
        } catch (Exception $ex) {
            return array('response' => 'error', 'data' => $ex->getMessage());
        }
    }

    private function _validateCredentials() {
        if (Config::get('bridgeapi.enabled')) {
            if (Sentinel::check() && $user = Sentinel::getUser()) {
                //TODO: Return user account information like email
                return array('response' => 'success', 'username' => $user->first_name);
            } else {
                throw new Exception('Not logged in.');
            }
        } else {
            throw new Exception('BridgeBB Internal auth API is disabled');
        }
    }

    public function missingMethod($parameters = array()) {
        return array(
            'response' => 'info',
            'data' => 'Not Implemented',
            'parameters' => $parameters);
    }

}