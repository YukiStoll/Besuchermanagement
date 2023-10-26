<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        Log::debug("1");
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request))
        {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        Log::debug("2");
        $request['username'] = env('ADLDAP_ACCOUNT_PREFIX', null) . $request['username'] . env('ADLDAP_ACCOUNT_SUFFIX', null);

        Log::debug("3");
        if ($this->attemptLogin($request, env("one_time_login", false))) {
            Log::debug("4");
            return $this->sendLoginResponse($request);
            Log::debug("5");
        }
        Log::debug("6");

        $this->incrementLoginAttempts($request);
        Log::debug("7");

        return $this->sendFailedLoginResponse($request);
    }
    protected function sendLoginResponse(Request $request)
    {

        $rememberTokenExpireMinutes = env('APP_REMEMBER_ME_TOKEN_LIFETIME', 120);

        $rememberTokenName = \Auth::getRecallerName();

        $cookieJar = $this->guard()->getCookieJar();

        $cookieValue = $cookieJar->queued($rememberTokenName)->getValue();

        $cookieJar->queue($rememberTokenName, $cookieValue, $rememberTokenExpireMinutes);

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }
}
