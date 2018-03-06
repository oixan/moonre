<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\User;
use JWTAuth;
use Countries;


class UserController extends Controller
{

    public function __construct(){
        $this->middleware('jwt.auth',[ 'only' => [
            'show', 'resetPassword', 'destroyWithPassword'
        ]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // only test
        //$country = Countries::all()->pluck('name.common','cca2');
        //$country = Countries::where('cca2',"IT")->pluck('name.common','cca2');
        
        /*$user = User::find(1);
        $user->city; 

        return  response()->json([
            'userCIty' => $country
        ]); */
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (! $userToken = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }

        if ( !$user ){
            return response()->json([ 'message'=>'user not found'], 404);
        }

        if ( $userToken->id != $user->id )
            return response()->json([ 'message'=>'Unauthorized'], 401);

        $user->city;
        if ( $user->country )
            $user->country_extended = Countries::where('cca2', strtoupper($user->country) )->pluck('name.common');

        return  response()->json([
            'user' => $user,
            'message' => 'Get User success!'
        ]);
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
        $sex = ['m','f'];

        $request->validate([ 
            'name' => 'nullable|max:200|alpha',
            'surname' => 'nullable|max:200|alpha',
            'sex' =>  Rule::in($sex),
            'country' => 'nullable|size:2',
            'city_id' => 'nullable|integer|exists:cities,id',
            'address' => 'nullable|string'
        ]);

        if (! $userToken = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }

        if (! $user = User::find($id) ){
            return response()->json([ 'message'=>'user not found'], 404);
        }

        if ( $user->id == $userToken->id ){
            
            if ( $request->has('name') && $request->filled('name') )
                $user->name = $request->input('name');
            elseif ( $request->has('name') && !$request->filled('name') )
                $user->name = "";

            if ( $request->has('surname') && $request->filled('surname') )
                $user->surname =  $request->input('surname');
            elseif ( $request->has('surname') && !$request->filled('surname') )
                $user->surname = "";

            if ( $request->has('sex') && $request->filled('sex') )
                $user->sex = $request->input('sex');
            elseif ( $request->has('sex') && !$request->filled('sex') )
                $user->sex = "";
            
            if ( $request->has('address') && $request->filled('address') )
                $user->address = $request->input('address');
            elseif ( $request->has('address') && !$request->filled('address') )
                $user->address = "";

            $state = null;
            if ( $request->has('country') && $request->filled('country') ){
                $country = $request->input('country');
                $state = Countries::where( 'cca2', strtoupper($country) )->pluck('name.common');
                if ( $state && count($state) <= 0 && $request->input('country') != null ){
                    return response()->json([ 
                        'message'=>'The given data was invalid.',
                        'errors' => (object)[
                            'country' => 'The selected country id is invalid.'
                        ],
                    ], 401);
                }
                $user->country = $country;
            }elseif ( $country = $request->has('country') && !$request->filled('country') ){
                $user->country = "";
            }

            if ( $request->has('city_id') && $request->filled('city_id'))
                $user->city_id = $request->input('city_id');
            elseif ( $request->has('city_id') && !$request->filled('city_id'))
                $user->city_id = 0;

            $user->save();

            $user->country_extended = $state;
            $user->city;
            
            return response()->json([ 
                'message'=>'Update Completed',
                'user' => $user 
            ], 200);
        }else{
            return response()->json([ 'message'=>'Unauthorized'], 401);
        }
        
        return response()->json([ 'message'=>'Error Unknown'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }


    /**
     * Delete Account with Password
     * 
     */
    public function destroyWithPassword(Request $request, $id){
        $request->validate([ 
            'password' => 'required|min:6|max:250',
        ]);

        if ( !$id )
            return response()->json([ 
                'message'=>'Error id empty!'
            ], 401 );

        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'Token user not found'], 404);
        }

        if (  !Hash::check( $request->input("password"), $user->password ) ) {
            return response()->json([ 
                'message'=>'Error password!'
            ], 401 );
        }

        if ( $userDB = User::find($id) ){
            if ( $userDB->id == $user->id){
                if ( $userDB->delete() ){
                    $response = [
                        'message' => 'Element deleted!',
                        'user' => $user
                    ];
                    return response()->json($response,200); 
                }else{
                    $response = [
                        'message' => 'Error delete element!'
                    ];
                    return response()->json($response,404); 
                }
            }else{
                $response = [
                    'message' => 'Error delete this user!'
                ];
                return response()->json($response,404); 
            }
        }

        $response = [
            'message' => 'Error operation!'
        ];
        
        return response()->json($response,404); 
    }


    /**
     * Reset Password
     * 
     */
    public function resetPassword(Request $request){
        $request->validate([ 
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6|max:250',
            'confirmNewPassword' => 'required|same:newPassword'
        ]);

        if (! $user = JWTAuth::parseToken()->authenticate()) {
			return response()->json([ 'message'=>'user not found'], 404);
        }

        if ( Hash::check( $request->input("oldPassword"), $user->password ) ) {

            $user->password = bcrypt( $request->input("newPassword") );
            $user->save();

            return response()->json([ 
                'message'=>'Password Changed Succesfull!'
            ], 200 );
        }else{
            return response()->json([ 
                'message'=>'Old password dont match'
            ], 403 );
        }
    }

    /**
     *  Verify Account Token Registration
     * 
     */
    public function verifyAccountToken($email,$token){
        $user = User::where('verifyAccountToken',$token)->get()->first();

        if ( $user ){
            if ( $user->verifyAccountToken ){
                $user->verifyAccountToken = null;
                $user->verify = true;
                if ( !$user->save() )
                    return redirect("/#/login");
                
                return redirect("/#/login");
            }
        }

        return redirect("/#/login");
    }
}
