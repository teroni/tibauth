<?php namespace Tib\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Sentinel;
use Illuminate\Http\Request;
use \Redirect;
use \Input;

class AuthController extends Controller {

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
        return view('tib.auth.index');
	}
	public function getUser()
	{
		$user = Sentinel::getUser();

		$data = [
		   '3' => ['testoffng']
		];

		$user->fill($data)->update();
		$user->save();
	}
	public function getRemoveapi($id)
	{
		//
		$user = Sentinel::getUser();
		$api = \Tib\Models\API::find($id);
		if($user->hasAccess(['director']) || $user->id == $api->user_id)
		{
			$chars = $user->characters()->where('user_apis_id', '=', $id)->delete();
			\Alert::info('API Deleted');
			$api->delete();
		}
		return Redirect::to('auth');
        
	}

	public function getAddapi()
	{
		return view('tib.auth.add');
	}
	public function postAddapi()
	{
		//return Input::all();
		if( \Tib\Models\API::where('apiid', '=', Input::get('keyid'))->first())
		{
			\Alert::warning('API Already Added');
			return view('tib.auth.add');
		}
		
		$params = [];

		$params[] = [
			'key' => 'keyID',
			'value' => Input::get('keyid'),
		];
		$params[] = [
			'key' => 'vCode',
			'value' => Input::get('vcode'),
		];
		
		$characters = \Tib\Models\EVE::CharacterList($params);
		$charlist = [];
		$charstring = "";
		$corpstring = "";
		foreach($characters->result->rowset->row as $char)
		{
			$charlist[] = array(
				'name' => $char['name'],
				'charid' => $char['characterID'],
				'corporation' => $char['corporationName'],
				'corpid' => $char['corporationID'],
				'alliance' => $char['allianceName'],
				'allianceid' => $char['allianceID'],
			);
			$charstring = $charstring .$char['name'] .  ", ";
			$corpstring = $corpstring .$char['corporationName']. ", ";
		}
		$api = new \Tib\Models\API;
		$api->user_id = Sentinel::getUser()->id;
		$api->apiid = Input::get('keyid');
		$api->vcode = Input::get('vcode');
		$api->charname = rtrim($charstring, ', ');
		$api->corporation = rtrim($corpstring, ', ');
		$api->save();
		// Let's insert the characters now.
		foreach($charlist as $char)
		{
			$character = new \Tib\Models\Character;
			$character->user_id = Sentinel::getUser()->id;
			$character->name = $char['name'][0];
			$character->charid = $char['charid'][0];
			$character->user_apis_id = $api->id;
			$character->corporation = $char['corporation'][0];
			$character->corporation_id = $char['corpid'][0];
			$character->alliance = $char['alliance'][0];
			$character->alliance_id = $char['allianceid'][0];
			$character->save();
		}
		\Alert::success('API Added');
		\Tib\Commands\RefreshRoles::refreshUser(\Tib\Models\User::find(Sentinel::getUser()->id));
		return Redirect::to('auth');
		//return view('tib.auth.add');
	}

}
