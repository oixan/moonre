<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mood;
use App\User;
use JWTAuth;

class LikeController extends Controller
{
    public function __construct(){
        $this->middleware('jwt.auth',[ 'only' => [
            'store', 'update', 'destroy'
        ]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'idMood'=> 'required'
        ]);

        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 401);
        }

        $idMood = $request->input('idMood');
        if ( $mood = Mood::find($idMood) ){
                $moodeLikedByUser = $user ->likes()
                                        ->where('user_like_mood.user_id',$user->id)
                                        ->where('user_like_mood.mood_id',$idMood)
                                        ->get()->first();

                if ( $moodeLikedByUser )
                    return response()->json([ 'message'=> 'User liked mood already!' ], 400);
                else
                    $user->likes()->attach($idMood);

                return response()->json([ 'message'=> 'User liked mood success!' ], 200);
        }

        return response()->json([ 'message'=>'Mood not found'], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ( $mood = Mood::find($id) ){

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([ 'message'=>'user not found'], 401);
            }

            if ( $user->likes()->detach($mood->id) ){
                $response = [
                    'message' => 'Like removed!',
                    'mood' => $mood
                ];
                return response()->json($response,200); 
            }else{
                $response = [
                    'message' => 'Like not found!'
                ];
                return response()->json($response,404); 
            }
        }

        $response = [
            'message' => 'Mood not found!'
        ];
        
        return response()->json($response,404); 
    }
}
