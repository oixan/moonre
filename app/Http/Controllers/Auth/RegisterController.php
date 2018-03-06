<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\UserVerification;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        /*
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]); */
    }

    public function signup(Request $request)
    {
        $request->validate([
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|max:250',
            'password_confirm'=>'required|same:password',
            'term' => 'accepted'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            "email"=>$email, 
            "password"=>bcrypt($password),
            "verifyAccountToken"=> Str::random(40)
        ]);
        
        if( $user->save() ){
            $response = [
                'message'=>'User created!',
                'user'=>$user
            ];
            Mail::to($user->email)->send(new UserVerification($user));

            return response()->JSON($response,201);
        }
        $response = [
            'message'=>'Error occured!',
        ];

        return response()->JSON($response,400);
    } 
}
