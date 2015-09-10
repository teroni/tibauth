<?php namespace Tib\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \Tib\Models\User as User;

class RefreshRoles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'roles:refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh user roles.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Get the list of users
		$users = \Tib\Models\User::all();
		
		foreach($users as $user)
		{
			$this->refreshUser($user);
		}
	}

	public static function refreshUser($user)
	{
			$roles = [];
			$roles[] = 2;
			// Get site level acl first
			if($user->hasAccess('director'))
			{
				$roles[] = 3;
			}
			if($user->hasAccess('superuser'))
			{
				$roles[] = 1;
			}
			// Now api based
			$alliances = $user->characters()->groupBy('alliance_id')->where('alliance_id', '>', 0)->get();
			$corps = $user->characters()->groupBy('corporation_id')->where('corporation_id', '>', 0)->get();
			foreach($alliances as $alliance)
			{
				if(in_array($alliance->alliance_id, \Config::get('tib.config.alliances')))
				{
					$roles[] = 4;
				}
				if(in_array($alliance->alliance_id, \Config::get('tib.config.blues')))
				{
					$roles[] = 5;
				}
			}
			foreach($corps as $corp)
			{
				if(in_array($corp->corporation_id, \Config::get('tib.config.corps')))
				{
					$role = \Sentinel::findRoleByName($corp->corporation);
					if(!$role)
					{
						$slug = str_replace(' ', '', snake_case($corp->corporation));
						$role = \Sentinel::getRoleRepository()->createModel();
						$role->slug = $slug;
						$role->name = $corp->corporation;
						$role->permissions = array($slug => true);
						$role->save();
						$authrole = new \Tib\Models\AuthGroups;
						$authrole->name = $corp->corporation;
						$authrole->acl = $slug;
						$authrole->save();
					}
					$roles[] = $role->id;
				}
			}
			// Now get the open roles so we dont lose them.
			foreach($user->roles()->where('open', '=', 1)->withPivot('owner', 'moderator', 'user_id', 'created_at')->get() as $role)
			{
				//echo $role->pivot->created_at;
				//print_r($role->pivot);
				$roles[] = array('owner' => $role->pivot->owner, 'moderator' => $role->pivot->moderator, 'created_at' => $role->pivot->created_at, 'user_id' => $role->pivot->user_id, 'role_id' => $role->id);
			}
			//print_r($roles);
			$user->roles()->sync($roles);
	}
}
