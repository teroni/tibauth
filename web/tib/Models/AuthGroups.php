<?php namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;

class AuthGroups extends Model {
    protected $table = 'auth_groups';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function role()
    {
      return $this->hasOne('\Tib\Models\Role', 'slug', 'slug');
    }

}
