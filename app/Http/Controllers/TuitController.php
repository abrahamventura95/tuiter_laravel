<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tuit;
use App\Like;
use App\Block;
use App\Follow;
use App\User;

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
        if(isset($request->ref)){
        	$tuit = Tuit::find($request->ref);
        	$user = User::find($tuit->user_id);
        	$block = Block::where('block_id','=',auth()->user()->id)
        				  ->where('user_id','=',$user->id)
        				  ->get();
        	if($request->type === 'rt' && $user->privacity){
        		return response()->json([
				            'message' => 'Cannot create rt this tuit!'
				        ], 401);
        	}			  
        	if(!sizeof($block)){
        		if($user->privacity){
        			$friendTo = Follow::where('follow_id','=',auth()->user()->id)
        							  ->where('status','=','1')
        							  ->get();				  
      				if(!sizeof($friendTo)){			
				        return response()->json([
				            'message' => 'Cannot create the tuit!'
				        ], 401);
      				}
        		}
        	}else{
		        return response()->json([
		            'message' => 'Cannot create the tuit!'
		        ], 401);
        	}
        }
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
    	$user = User::find($tuit[0]->user_id);
    	$block = Block::where('block_id','=',auth()->user()->id)
    				  ->where('user_id','=',$user->id)
    				  ->get();		  
    	if(!sizeof($block)){
    		if($user->privacity){
    			$friendTo = Follow::where('follow_id','=',auth()->user()->id)
    							  ->where('status','=','1')
    							  ->get();				  
  				if(!sizeof($friendTo)){			
			        return response()->json([
			            'message' => 'Cannot show the tuit!'
			        ], 401);
  				}
    		}
    	}else{
	        return response()->json([
	            'message' => 'Cannot show the tuit!'
	        ], 401);
    	}		      
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
        $tuit = Tuit::find($request->tuit);
    	$user = User::find($tuit->user_id);
    	$block = Block::where('block_id','=',auth()->user()->id)
    				  ->where('user_id','=',$user->id)
    				  ->get(); 
    	if(!sizeof($block)){
    		if($user->privacity){
    			$friendTo = Follow::where('follow_id','=',auth()->user()->id)
    							  ->where('status','=','1')
    							  ->get();				  
  				if(!sizeof($friendTo)){			
			        return response()->json([
			            'message' => 'Cannot like this tuit!'
			        ], 401);
  				}
    		}
    	}else{
	        return response()->json([
	            'message' => 'Cannot like this tuit!'
	        ], 401);
    	}
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
    // Blocks
    /**
     * Create a block
     */
    public function createBlock(Request $request){
        $request->validate([
            'user' => 'required|exists:App\User,id'
        ]);

        Block::create([
            'user_id' => auth()->user()->id,
            'block_id' => $request->user
        ]);

        return response()->json([
            'message' => 'Successfully blocked!'
        ], 201);
    }
    /**
     * Delete a block
     */
    public function deleteBlock($id){
    	$block = Block::where('user_id','=',auth()->user()->id)
    				->where('block_id','=',$id)
    				->get();
        if(isset($block)){
        	$block[0]->delete();
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
     * Show all user`s blocks
     */
    public function getBlocks(Request $request){
    	return Block::join('users','users.id','=','blocks.block_id')
    			    ->select('blocks.id', 'users.name as username', 'users.email', 'users.id as userId')
    			    ->where('blocks.user_id','=',auth()->user()->id)
    			    ->orderBy('blocks.created_at','desc')
    			    ->get();
    }
    // Follows
    /**
     * Create a follow
     */
    public function createFollow(Request $request){
        $request->validate([
            'user' => 'required|exists:App\User,id'
        ]);
        $user = User::find($request->user);
      	Follow::create([
	            'user_id' => auth()->user()->id,
	            'follow_id' => $request->user,
	            'status' => !($user->privacity)
	    ]);

        return response()->json([
            'message' => 'Successfully followed!'
        ], 201);
    }
    /**
     * Delete a follow
     */
    public function deleteFollow($id){
    	$follow = Follow::where('user_id','=',auth()->user()->id)
    					->where('follow_id','=',$id)
    					->get();
        if(isset($follow)){
        	$follow[0]->delete();
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
     * Show all user`s follows & followers
     */
    public function getFollows($id){
    	if(!$id) $id = auth()->user()->id;
    	$follows = Follow::join('users','users.id','=','followers.follow_id')
    					 ->select('users.*','followers.status', 'followers.id as FollowId')
    					 ->where('followers.user_id','=',$id)
    					 ->orderBy('followers.status','desc')
    					 ->orderBy('followers.updated_at', 'desc')
    					 ->get();
    	$followers = Follow::join('users','users.id','=','followers.user_id')
    					   ->select('users.*','followers.status', 'followers.id as FollowId')
    					   ->where('followers.follow_id','=',$id)
    					   ->orderBy('followers.status','desc')
    					   ->orderBy('followers.updated_at', 'desc')
    					   ->get();				 
    	$resp = array('follows' => $follows, '$followers' => $followers);
    	return $resp;
    }
    /**
     * Edit a follow
     */
    public function acceptFollow($id){
    	$follow = Follow::find($id);
    	$follow->status = 1;
    	$follow->save();
        return $follow;
    }
    //Timeline
    public function get(Request $request){
    	return Tuit::join('followers','followers.follow_id','=','tuits.user_id')
    			   ->join('users','users.id','=','tuits.user_id')
    			   ->select('tuits.*','users.name as username','users.email')
    			   ->where('followers.user_id','=',auth()->user()->id)
    			   ->where('followers.status','=','1')
			       ->orderBy('tuits.created_at','desc')
		    	   ->get();
    }
}
