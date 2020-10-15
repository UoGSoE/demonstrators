<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Ldap;
use App\Course;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/';

    protected $ldap;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Ldap $ldap)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->ldap = $ldap;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        return $this->attemptLogin($request);
    }

    protected function attemptLogin(Request $request)
    {
        $username = trim(strtolower($request->username));
        $password = $request->password;
        $ldapUser = $this->ldap->authenticate($username, $password);
        if (! $ldapUser) {
            return $this->sendFailedLoginResponse($request);
        }
        $user = User::where('username', $username)->first();
        if (! $user) {
            $user = User::createFromLdap($ldapUser);
        }
        Auth::login($user, $remember = true);

        return $this->sendLoginResponse($request);
    }
}
