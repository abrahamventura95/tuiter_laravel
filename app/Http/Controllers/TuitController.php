<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tuit;
use App\Like;

class TuitController extends Controller{
    /**
     * Create a tuit
     */
    public function create(Request $request){
        $request->validate([
            'msg' => 'required|string',
            'type' => 'in:rt,quote,replay',
            'ref' => 'exists:App\Tuit,id'
        ]);

        Tuit::create([
            'user_id' => auth()->user()->id,
            'msg' => $request->msg,
            'type' => $request->type,
            'ref' => $request->ref
        ]);

        return response()->json([
            'message' => 'Successfully created tuit!'
        ], 201);
    }
    /**
     * Show all user`s tuits
     */
    public function getMine(Request $request){
    	return Tuit::where('user_id','=',auth()->user()->id)
    			      ->orderBy('created_at','desc')
    			      ->get();
    }
    /**
     * Show a tuit
     */
    public function show($id){
    	$tuit = Tuit::join('users','users.id','=','tuits.user_id')
    				  ->select('tuits.*', 'users.name as username', 'users.email')
    				  ->where('tuits.id','=',$id)
    			      ->orderBy('created_at','desc')
    			      ->get();
    	$responses = Tuit::join('users','users.id','=','tuits.user_id')
    				  	 ->select('tuits.*', 'users.name as username', 'users.email')
    				     ->where('tuits.ref','=',$id)
    			         ->orderBy('type','asc')
    			         ->orderBy('created_at','desc')
    			         ->get();	      
    	$likes = Tuit::join('likes','likes.tuit_id','=','tuits.id')
    			   	 ->join('users','users.id','=','tuits.user_id')
    			     ->select('tuits.*', 'users.name as username', 'users.email')
    			     ->where('likes.tuit_id','=',$id)
    			     ->orderBy('likes.created_at','desc')
    			     ->get();		         
    	$resp = array('tuit' => $tuit[0], 'responses' => $responses, 'likes' => $likes);				   
    	return $resp;
    }
    /**
     * Delete a tuit
     */
    public function delete($id){
    	$tuit = Tuit::find($id);
        if(isset($tuit) && $tuit->user_id === auth()->user()->id){
        	$tuit->delete();
	        return response()->json([
	            'message' => 'Successfully deleted!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted!'
	        ], 401);
    	}
    }
    //Likes
    /**
     * Create a like
     */
    public function createLike(Request $request){
        $request->validate([
            'tuit' => 'required|exists:App\Tuit,id'
        ]);

        Like::create([
            'user_id' => auth()->user()->id,
            'tuit_id' => $request->tuit
        ]);

        return response()->json([
            'message' => 'Successfully created like!'
        ], 201);
    }
    /**
     * Delete a like
     */
    public function deleteLike($id){
    	$like = Like::where('user_id','=',auth()->user()->id)
    				->where('tuit_id','=',$id)
    				->get();
        if(isset($like)){
        	$like[0]->delete();
	        return response()->json([
	            'message' => 'Successfully deleted!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted!'
	        ], 401);
    	}
    }

    /**
     * Show all user`s likes
     */
    public function getLikes(Request $request){
    	return Tuit::join('likes','likes.tuit_id','=','tuits.id')
    			   ->join('users','users.id','=','tuits.user_id')
    			   ->select('tuits.*', 'users.name as username', 'users.email')
    			   ->where('likes.user_id','=',auth()->user()->id)
    			   ->orderBy('likes.created_at','desc')
    			   ->get();
    }
}
