<?php namespace Tib\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Sentinel;
use Illuminate\Http\Request;
use \Redirect;
use \Input;

class RolesController extends Controller {

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
        return view('tib.roles.index');
	}
	public function getRequest($id)
	{
		$role = \Tib\Models\Role::find($id);
		$user = Sentinel::getUser();
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles');
		}
		if(!$role)
		{	
			\Alert::warning('Role Not Found');
			return Redirect::to('roles');
		}
		if(!$role->open)
		{
			\Alert::warning('Role is not open for members');
			return Redirect::to('roles');
		}
		if(Sentinel::inRole($role->slug))
		{
			\Alert::warning("You're already in ". $role->name);
			return Redirect::to('roles');
		}
		$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
		if($request)
		{
			\Alert::warning("You already have an outstanding request");
			return Redirect::to('roles');
		}

		$request = new \Tib\Models\RoleRequest;
		$request->user_id = $user->id;
		$request->role_id = $role->id;
		$request->save();
		\Alert::warning("Applied to group");
		return Redirect::to('roles');
	}
	public function getLeave($id)
	{
		$role = \Tib\Models\Role::find($id);
		$user = Sentinel::getUser();
		if(!$role)
		{	
			\Alert::warning('Role Not Found');
			return Redirect::to('roles');
		}
		if(!$role->open)
		{	
			\Alert::warning('Cannot leave this role');
			return Redirect::to('roles');
		}
		$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
		if($request)
		{
			$request->delete();
		}
		$role->users()->detach($user);
		\Alert::warning("Removed from role");
		return Redirect::to('roles');
	}
	public function getAccept($roleid, $userid)
	{
		$currentuser = \Tib\Models\User::find(Sentinel::getUser()->id);
		$user = \Tib\Models\User::find($userid);
		$role = \Tib\Models\Role::find($roleid);
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$role)
		{
			\Alert::warning('Role Not Found');
			return Redirect::to('roles/');
		}
		if(@!$role->open)
		{
			\Alert::warning('Role does not allow management');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if($user->hasAccess(['superuser']))
		{
			if($user->inRole($role->slug))
			{
				\Alert::warning("User already in role");
				return Redirect::to('roles');
			}

			$role->users()->attach($user);
			\Alert::info('User added to role');
			$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
			$request->delete();
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$currentuser->hasAccess('superuser') || !$currentuser->roles()->where('role_id', '=', $role->id)->first()->pivot->owner|| !$currentuser->roles()->where('role_id', '=', $role->id)->first()->pivot->moderator)
		{
			\Alert::warning('You do not have access');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if($user->inRole($role->slug))
		{
			\Alert::warning("User already in role");
			return Redirect::to('roles');
		}

		$role->users()->attach($user);
		\Alert::info('User added to role');
		$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
		$request->delete();
		return Redirect::to('roles/manage/'. $roleid);
	}
	public function getDeny($roleid, $userid)
	{
		$currentuser = \Tib\Models\User::find(Sentinel::getUser()->id);
		$user = \Tib\Models\User::find($userid);
		$role = \Tib\Models\Role::find($roleid);
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$role)
		{
			\Alert::warning('Role Not Found');
			return Redirect::to('roles/');
		}
		if(@!$role->open)
		{
			\Alert::warning('Role does not allow management');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if($user->hasAccess('superuser'))
		{
			\Alert::info('User denied to role');
			$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
			if(!$request)
			{
				\Alert::warning('Error occured, try again or contact Jeronica');
				return Redirect::to('roles/manage/'. $roleid);
			}
			$request->delete();
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$currentuser->hasAccess('superuser') || !$currentuser->roles()->where('role_id', '=', $role->id)->first()->pivot->owner|| !$currentuser->roles()->where('role_id', '=', $role->id)->first()->pivot->moderator)
		{
			\Alert::warning('You do not have access');
			return Redirect::to('roles/manage/'. $roleid);
		}
		\Alert::info('User denied to role');
		$request = \Tib\Models\RoleRequest::where('user_id', '=', $user->id)->where('role_id', '=', $role->id)->first();
		if(!$request)
		{
			\Alert::warning('Error occured, try again or contact Jeronica');
			return Redirect::to('roles/manage/'. $roleid);
		}
		$request->delete();
		return Redirect::to('roles/manage/'. $roleid);
	}
	public function getKick($roleid, $userid)
	{
		$user = \Tib\Models\User::find($userid);
		$role = \Tib\Models\Role::find($roleid);
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$role)
		{
			\Alert::warning('Role Not Found');
			return Redirect::to('roles/');
		}
		if(!$role->open)
		{
			\Alert::warning('Role does not allow management');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if($user->hasAccess('superuser'))
		{
			$role->users()->detach($user);
			\Alert::warning('User kicked from role');
			return Redirect::to('roles/manage/'. $roleid);
		}
		if(!$user->hasAccess('superuser') || !$user->roles()->where('role_id', '=', $role->id)->first()->pivot->owner|| !$user->roles()->where('role_id', '=', $role->id)->first()->pivot->moderator)
		{
			\Alert::warning('You do not have access');
			return Redirect::to('roles/manage/'. $roleid);
		}
		$role->users()->detach($user);
		\Alert::warning('User kicked from role');
		return Redirect::to('roles/manage/'. $roleid);
	}
	public function getManage($roleid)
	{
		$user = \Tib\Models\User::find(Sentinel::getUser()->id);
		$role = \Tib\Models\Role::find($roleid);
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles');
		}
		if(!$role)
		{
			\Alert::warning('Role Not Found');
			return Redirect::to('roles');
		}
		if(!$role->open)
		{
			\Alert::warning('Role does not allow management');
			return Redirect::to('roles');
		}
		if($user->hasAccess('superuser') || $role->users()->where('user_id', '=', $user->id)->first()->pivot_moderator || $role->users()->where('user_id', '=', $user->id)->first()->pivot_owner)
		{
			return view('tib.roles.manage')->with('role', $role);
		}
		if(@!$user->hasAccess('superuser') || @!$role->users()->where('user_id', '=', $user->id)->first()->pivot_moderator && @!$role->users()->where('user_id', '=', $user->id)->first()->pivot_owner)
		{
			\Alert::warning('You do not have access');
			return Redirect::to('roles');
		}

		// All checks look good, display the page
		return view('tib.roles.manage')->with('role', $role);
	}
	public function getSetauth($roleid)
	{
		if(!$user->hasAccess('superuser'))
		{
			\Alert::warning('You do not have access to this.');
			return Redirect::to('roles');
		}
	}
	public function getCreate()
	{
		$user = Sentinel::getUser();
		if(!$user->hasAccess('superuser'))
		{
			\Alert::warning('You do not have access to this.');
			return Redirect::to('roles');
		}
		return view('tib.roles.create');
	}
	public function postCreate()
	{
		$user = Sentinel::getUser();
		if(!$user->hasAccess('superuser'))
		{
			\Alert::warning('You do not have access to this.');
			return Redirect::to('roles');
		}
		$acl = explode(', ', Input::get('permissions'));
		$permission = [];
		foreach($acl as $perm)
		{
			$permission[$perm] = true;
		}
		$role = new \Tib\Models\Role;
		$role->slug = snake_case(Input::get('name'));
		$role->name = Input::get('name');
		$role->permissions = $permission;
		$role->open = 1;
		$role->save();

		$role->users()->attach($user);
		$user->roles()->updateExistingPivot($role->id, array('owner' => 1));

		\Alert::info('Role Created, and auto joined.');
		return Redirect::to('roles/manage/'. $role->id);
	}
	public function getGenauthgroup($roleid)
	{
		$user = \Tib\Models\User::find(Sentinel::getUser()->id);
		$role = \Tib\Models\Role::find($roleid);
		if(!$user->hasAccess(['superuser']))
		{
			\Alert::info('Check your privilege');
			return Redirect::to('roles/manage/'. $role->id);
		}
		if(!$user)
		{
			\Alert::warning('User Not Found');
			return Redirect::to('roles');
		}
		if(!$role)
		{
			\Alert::warning('Role Not Found');
			return Redirect::to('roles');
		}
		$authgroup = new \Tib\Models\AuthGroups;
		$acl = [];
		foreach($role->permissions as $perm => $value)
		{
			if($value == 1)
			{
				$acl[] = $perm;
			}
		}
		$authgroup->slug = $role->slug;
		$authgroup->name = $role->name;
		$authgroup->acl = json_encode($acl);
		$authgroup->channels = json_encode($acl);
		$authgroup->save();
		\Alert::info('Auth Group Generated. Please claim channel.');
		return Redirect::to('roles/manage/'. $role->id);

	}
}
