<?php namespace Tib\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class API extends Model {
	protected $table = 'user_apis';
	use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	//
	public function user()
    {
    	Sentinel::setModel('Tib\Models\User');
        return $this->belongsTo('Tib\Models\User');
    }
    public function characters()
    {
        return $this->belongsTo('Tib\Models\Character');
    }

}
