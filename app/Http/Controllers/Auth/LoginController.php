<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;

use Countries;
use JWTAuth;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function signin(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $credential = $request->only('email','password');

        $user = User::where('email', $credential['email'])->first();
        
        if ( $user != null && !$user->verify )
            return response()->JSON([
                'message' => 'User non verifity yet !  Check your email account !'
            ], 401);

        try{
            if ( ! $token = JWTAuth::attempt($credential) ){
                return response()->JSON(['message' => 'Invalid Credentials'], 401);
            }
        }catch(JWTException $e){
            return response()->JSON(['message' => 'Unknown Error'], 401);
        }

        return response()->JSON([
            'message' => 'Login Success!',
            'token' => $token,
            'user' => (Object) [ 
                        'id' => $user->id,
                        'email' => $user->email,
                    ]
        ], 200);
    }
}
