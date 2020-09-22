<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tuit extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tuits';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'msg','ref','type','user_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'ref','user_id'
    ];
}
