<?php

namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Attributes\EntityInterface;
use Cartalyst\Attributes\EntityTrait;

class User extends \Cartalyst\Sentinel\Users\EloquentUser implements EntityInterface
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    use EntityTrait;
    public function apis()
    {
        return $this->hasMany('Tib\Models\API');
    }
    public function characters()
    {
        return $this->hasMany('Tib\Models\Character');
    }
    public function getAttributes()
    {
        $attributes = [];
                foreach(\Sentinel::getUser()->values as $val)
                {
                  $attributes[$val->attribute->slug] = $val->value;
                }
                return $attributes;
    }
    public function roles()
    {
      return $this->belongsToMany('Tib\Models\Role', 'role_users')->withPivot('owner', 'moderator');
    }
}