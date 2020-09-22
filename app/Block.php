<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'block_id','user_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'block_id','user_id'
    ];
}
