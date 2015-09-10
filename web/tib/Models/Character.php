<?php namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Character extends Model {
	protected $table = 'user_characters';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
	//
	public function user()
    {
        return $this->belongsTo('Tib\Models\User');
    }
    public function API()
    {
        return $this->belongsToMany('Tib\Models\API');
    }
}
