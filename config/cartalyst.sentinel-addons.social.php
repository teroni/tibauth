<?php
use \TIB\Providers;
/**
 * Part of the Sentinel Social package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel Social
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | Connections are simple. Each key is a unique slug for the connection. Use
    | anything, just make it unique. This is how you reference it in Sentinel
    | Social. Each slug requires a driver, which must match a valid inbuilt
    | driver or may match your own custom class name that inherits from a
    | valid base driver.
    |
    | Make sure each connection contains an "identifier" and a "secret". Thse
    | are also known as "key" and "secret", "app id" and "app secret"
    | depending on the service. We're using "identifier" and
    | "secret" for consistency.
    |
    | OAuth2 providers may contain an optional "scopes" array, which is a
    | list of scopes you're requesting from the user for that connection.
    |
    | You may use multiple connections with the same driver!
    |
    */

    'connections' => [
        'Evesso' => [
            'driver'     => '\Tib\Providers\Ccpsso',
            'identifier' => '8eb2c9798fe84462abcd9b5420d49a5f',
            'secret'     => 'sqpR3NwqKMKQFoqFQWjcgMdouPcIBRFSsEDKtH5M',
            'scopes'     => [
                'publicData',
            ],
        ]
    ],
 
    /*
    |--------------------------------------------------------------------------
    | Link Model
    |--------------------------------------------------------------------------
    |
    | When users are registered, a "link repository" will map the social
    | authentications with user instances. Feel free to use your own model
    | with our provider.
    |
    */

    'link' => 'Cartalyst\Sentinel\Addons\Social\Models\Link',

];
