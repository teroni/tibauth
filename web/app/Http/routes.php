<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::controller('auth', '\Tib\Controllers\AuthController');
Route::controller('roles', '\Tib\Controllers\RolesController');
Route::controller('services', '\Tib\Controllers\ServicesController');
Route::controller('irc', '\Tib\Controllers\IRCController');
Route::post('phpbb/authorize', function()
{
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $request->query['redirect_uri'] = null;
    // validate the authorize request
    if (!App::make('oauth2')->validateAuthorizeRequest($request, $response)) {
        $response->send();
        die;
    }
    // print the authorization code if the user has authorized your client
    $is_authorized = ($_POST['authorized'] === 'yes');

    App::make('oauth2')->handleAuthorizeRequest($request, $response, $is_authorized, Sentinel::getUser()->id);
    if ($is_authorized) {
      // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
      $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
      //exit("SUCCESS! Authorization Code: $code");
  }
    //return Redirect::to($response->getHttpHeader('Location'));
    $response->send();
});
Route::get('phpbb/authorize', function()
{
    return view('tib.oauth.authorization-form');
});

Route::any('phpbb/token', function(){
    App::make('oauth2')->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
}); 
Route::any('phpbb/resource', function()
{
    $request = OAuth2\Request::createFromGlobals();
// Handle a request to a resource and authenticate the access token
if (!App::make('oauth2')->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    die;
}
$token = App::make('oauth2')->getAccessTokenData(OAuth2\Request::createFromGlobals());
$user = Sentinel::findById($token['user_id']);
$groups = [];

foreach($user->roles as $role)
{
    $groups[] = $role->slug;
}

return json_encode(array('name' => $user->first_name, 'permissions' => $groups, 'id' => $user->id, 'registered' => $user->created_at));
});
App::singleton('oauth2', function() {
    
    $storage = new OAuth2\Storage\Pdo(DB::connection()->getPdo()); 
    $server = new OAuth2\Server($storage);
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    $scope = new OAuth2\Scope(array(
      'supported_scopes' => array('first_name', 'user_id')
    ));
    $server->setScopeUtil($scope);
    return $server; 
});

Route::post('oauth/token', function()
{
    $bridgedRequest  = OAuth2\HttpFoundationBridge\Request::createFromRequest(Request::instance());
    $bridgedResponse = new OAuth2\HttpFoundationBridge\Response();
    
    $bridgedResponse = App::make('oauth2')->handleTokenRequest($bridgedRequest, $bridgedResponse);
    
    return $bridgedResponse;
});
Route::get('oauth/authorize', function()
{
    $callback = URL::to('oauth/callback');
    $url = Social::getAuthorizationUrl('Evesso', $callback);
    return Redirect::to($url);
});
Route::get('oauth/callback', function()
{

    // Callback is required for providers such as Facebook and a few others (it's required
    // by the spec, but some providers omit this).
    $callback = URL::current();
    if(Sentinel::check())
    {
        Session::flush();
    }
    try
    {
        $user = Social::authenticate('Evesso', URL::current(), function(\Cartalyst\Sentinel\Addons\Social\Models\LinkInterface $link, $provider, $token, $slug)
        {
            // Retrieve the user in question for modificiation
            $user = $link->getUser();


            // You could add your custom data

            $data = $provider->getUserDetails($token);
            $user->save();
        });
    }
    catch (Error $e)
    {
        // Missing OAuth parameters were missing from the query string.
        // Either the person rejected the app, or the URL has been manually
        // accesed.

        App::abort(404);
    }
    return Redirect::to('profile');
});
