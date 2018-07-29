<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    function googleRedirect()
    {
        return \Socialite::driver('google')->redirect();
    }

    function googleHandle()
    {
        $googleUser = \Socialite::driver('google')->user();
        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            \Auth::login($user);
            return \Redirect::to($this->redirectPath());
        } else {
            flash('You are not authorized to login');
            return \Redirect::to('/login');
        }
    }
}
