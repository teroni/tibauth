<?php namespace Tib\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Sentinel;
use Illuminate\Http\Request;
use \Redirect;
use \Input;
use Cartalyst\Attributes\Attribute;

class ServicesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
		public function __construct()
    {
        $this->beforeFilter('auth');
    }
	public function getIndex()
	{
		//
        return view('tib.services.index');
	}
	public function postIndex()
	{
		//
		$user = Sentinel::getUser();
		if(Input::has('ts3id'))
		{
			$attributerow = \Tib\Models\Attributes::where('entity_id', '=', $user->id)->where('attribute_id', '=', $user->id)->first();
			if(!$attributerow)
			{
				$attributerow = new \Tib\Models\Attributes;
				$attributerow->attribute_id = 3;
				$attributerow->entity_id = $user->id;
				$attributerow->entity_type = 'Tib\Models\User';
			}
			$attributerow->value = Input::get('ts3id');
			$attributerow->save();
		}
		if(Input::has('ircpass'))
		{
			$attributerow = \Tib\Models\Attributes::where('entity_id', '=', $user->id)->where('attribute_id', '=', 5)->first();
			if(!$attributerow)
			{
				$attributerow = new \Tib\Models\Attributes;
				$attributerow->attribute_id = 5;
				$attributerow->entity_id = $user->id;
				$attributerow->entity_type = 'Tib\Models\User';
				$attributerow->value = Input::get('ircpass');
				$attributerow->save();
				// Also fire a new user
				return Redirect::to('irc/reregister');

			} else {
				$attributerow->value = Input::get('ircpass');
				$attributerow->save();
			}
		}

		
		return Redirect::to('services');
	}
	public function getTs3($user = null)
	{
		if(!$user)
		{
			$userid = Sentinel::getUser();
			$user = \Tib\Models\User::find($userid->id);
		}
		if(\Cache::has($user->id. 'refreshts3token'))
		{
			$now = \Carbon::now();
			$time = \Carbon::parse(\Cache::get($user->id. 'refreshts3token'));
			//@\Alert::error('You cannot refresh that often. Please try again in '. $now->diffInSeconds($time) . ' seconds');
			//return Redirect::to('services');
		} else {
			//\Cache::put($user->id. 'refreshts3token', \Carbon::now()->addSeconds(60)->toDateTimeString(), 1);
		}
		$ts3_VirtualServer = \TeamSpeak3::factory("serverquery://". config('tib.config.ts3_queryuser').":". config('tib.config.ts3_querypass')."@". config('tib.config.ts3_server').":". config('tib.config.ts3_queryport')."/?server_port=". config('tib.config.ts3_serverport')."&nickname=". config('tib.config.ts3_nickname')."");
		// Init if needed;
		try
		{
			$ts3_Client = $ts3_VirtualServer->clientGetByUid($user['ts3-identity']);
			
		}
		catch(\TeamSpeak3_Adapter_ServerQuery_Exception $e)
		{
			\Alert::error('User Not Found in TS3. Please connect.');
			return Redirect::to('services');
		}

		$groups = \Tib\Models\AuthGroups::all();

		foreach($groups as $group)
		{
			try{
				@$ts3group = $ts3_VirtualServer->serverGroupGetByName($group->name);

			}
			catch(\TeamSpeak3_Adapter_ServerQuery_Exception $e)
			{
				$ts3_VirtualServer->serverGroupCreate($group->name);
				$ts3group = $ts3_VirtualServer->serverGroupGetByName($group->name);
			}
			if($user->hasAccess(json_decode($group->acl)) && $ts3group['sgid'])
			{
				try
				{
					$ts3_VirtualServer->serverGroupClientAdd($ts3group['sgid'], $ts3_Client['client_database_id']);
				} catch (\TeamSpeak3_Adapter_ServerQuery_Exception $e)
				{
					//
				}
				
			} else {
				try
				{
					$ts3_VirtualServer->serverGroupClientDel($ts3group['sgid'], $ts3_Client['client_database_id']);
				} catch (\TeamSpeak3_Adapter_ServerQuery_Exception $e)
				{
					//
				}

				
			}

		}
		$edit['client_description'] = $user->first_name;
		$ts3_Client->modify($edit);
		\Alert::info('TS3 Roles Refreshed');
		return Redirect::to('services');
	}

}