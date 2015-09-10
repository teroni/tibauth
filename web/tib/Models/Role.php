<?php

namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;
use \Sentinel;

class Role extends \Cartalyst\Sentinel\Roles\EloquentRole
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $appends = ['request'];
    public function request($user = null)
    {
      if(!$user && Sentinel::check())
      {
        $user = Sentinel::getUser();
      }
      return $this->hasOne('\Tib\Models\RoleRequest')->where('user_id', '=', $user->id);
    }
    public function requests()
    {
      return $this->hasMany('\Tib\Models\RoleRequest')->with('users');
    }
    public function users()
    {
      return $this->belongsToMany('\Tib\Models\User', 'role_users', 'role_id')->withPivot('created_at', 'owner', 'moderator');
    }
    public function authgroup()
    {
      return $this->hasOne('\Tib\Models\AuthGroups', 'slug', 'slug');
    }

}