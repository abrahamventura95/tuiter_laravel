<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'likes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'tuit_id','user_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'tuit_id','user_id'
    ];
}
