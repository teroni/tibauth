<?php

namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleRequest extends \Cartalyst\Sentinel\Roles\EloquentRole
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $table = 'role_request';
    use SoftDeletes;
    public function users()
    {
      return $this->hasOne('\Tib\Models\User', 'id', 'user_id');
  }
}