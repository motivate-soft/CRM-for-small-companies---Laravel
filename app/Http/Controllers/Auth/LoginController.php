<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CompanyRegistered;
use App\Models\Employee;
use App\User;
use App\Models\WebToken;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    protected $data = []; // the information we send to the view

    protected $maxAttempts = 3;

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
    use AuthenticatesUsers {
        logout as defaultLogout;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard", ['except' => 'logout']);

        // ----------------------------------
        // Use the admin prefix in all routes
        // ----------------------------------

        // If not logged in redirect here.
        $this->loginPath = property_exists($this, 'loginPath') ? $this->loginPath
            : backpack_url('login');

        // Redirect here after successful login.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : backpack_url('dashboard');

        // Redirect here after logout.
        $this->redirectAfterLogout = property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout
            : backpack_url();
    }

    function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
        ]);

        $user_id = $user->id;
        $device_token = $request->device_token;
		if($device_token != ''){
			$token = WebToken::where('user_id', $user_id)->first();
			if(!$token){
				$web_token = new WebToken();
				$web_token->user_id = $user_id;
				$web_token->token = $device_token;
				$web_token->save();
			}else{
				WebToken::where('user_id', $user_id)->update(['token'=>$device_token]);
			}
		}
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => [
                'required',
                'string',
                function($attribute, $value, $fail) {
                    if ($attribute === 'email') {
                        $user = User::where('email', $value)->first();
                        if($user) {
                            if($user->status == User::STATUS_BANNED) {
                                return $fail(trans('fields.account_banned'));
                            }
                            if($user->status == User::STATUS_DISABLED) {

                                $user->registration_code = sprintf("%06d", random_int(1, 999999));
                                $user->registration_code_expired = Carbon::now()->addHours(config('auth.register_code_expired_hours'))->toDateTimeString();

                                $user->save();

                                Mail::to($user->email)->send(new CompanyRegistered($user->registration_code));

                                return $fail(trans('fields.account_disabled') . '. ' . ucfirst(trans('fields.please')) . ', <a target="_blank" href="'. route('backpack.auth.register.confirm') .'">'. trans('fields.activate_by_email') .'</a>');
                            }
                        }
                    }
                }
            ],
            'password' => 'required|string',
        ]);
    }

    /**
     * Return custom username for authentication.
     *
     * @return string
     */
    public function username()
    {
        return backpack_authentication_column();
    }

    /**
     * Log the user out and redirect him to specific location.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
//        $user_id = $this->guard()->user()->id;
//        if($this->guard()->user()->role == 'employee'){
//            $employee_id = Employee::where('user_id', $user_id)->first()->id;
//            WebToken::where('employee_id', $employee_id)->delete();
//        }
        // Do the default logout procedure
        $this->guard()->logout();

        // And redirect to custom location
        return redirect($this->redirectAfterLogout);
    }

    /**
     * Get the guard to be used during logout.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }

    // -------------------------------------------------------
    // Laravel overwrites for loading backpack views
    // -------------------------------------------------------

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $this->data['title'] = trans('backpack::base.login'); // set the page title
        $this->data['username'] = $this->username();

        return view('backpack::auth.login', $this->data);
    }
}
