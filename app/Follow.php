<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'followers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'follow_id','user_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'follow_id','user_id'
    ];
}
