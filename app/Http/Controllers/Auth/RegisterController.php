<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CompanyRegistered;
use App\Models\ActionsOption;
use App\Models\Company;
use App\Models\CompanyPlan;
use App\Models\Country;
use App\Models\Device;
use App\Models\Plan;
use App\Models\Soap;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Validator;

class RegisterController extends Controller
{
    protected $data = []; // the information we send to the view

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard");

        // Where to redirect users after login / registration.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : config('backpack.base.route_prefix', 'dashboard');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();
        $email_validation = backpack_authentication_column() == 'email' ? 'email|' : '';

        return Validator::make($data, [
            'name'                             => 'required|max:255',
            backpack_authentication_column()   => 'required|'.$email_validation.'max:255|unique:'.$users_table,
            'password'                         => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
		$country_id = 1;  // default country
        $country = $data['country'];
        $countries = Country::all();
        foreach ($countries as $country) {
            if ($country['name'] == $country) {
                $country_id = $country['id'];
                break;
            }
        }
        $holiday_days = Country::find($country_id)->holiday_days;
		
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $user = $user->create([
            'name'                             => $data['name'],
            backpack_authentication_column()   => $data[backpack_authentication_column()],
            'password'                         => bcrypt($data['password']),
            'role'      => User::ROLE_COMPANY,
            'status'    => User::STATUS_DISABLED,
            'registration_code'    => $data['registration_code'],
            'registration_code_expired'    => $data['registration_code_expired'],
			'holiday_days' => $holiday_days,
        ]);
	$plan = Cookie::get('biodactil_plan');	
		// default holiday days
        $working_days = Country::find($country_id)->workingDays->pluck('id');
        $user_id = $user->id;
        User::find($user_id)->workingDays()->attach($working_days);
		
        $soap = new Soap();

        $client = $soap->clientSave([
            'name' => $data['name'],
            'postUrl' => route('api.devices'),
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'access_company_token' => Company::createAccessToken(),
            'client_id' => $client->result->clientId,
        ]);

        Company::createMandatoryEntities($company->id);
        $plan_id = explode( '-', $plan)[0];
        $type = explode( '-', $plan)[1];

        $companyPlan = new CompanyPlan();
        $companyPlan->plan_id = $plan_id;
        $companyPlan->company_id = $company->id;
        $companyPlan->billing_status = 'free';
        $companyPlan->company_plan_id = 'biodactil_plan-' . $plan_id . '_' . $type;

        $free_month = Plan::find($plan_id)->free_month;
        $expre_date = date('Y-m-d', strtotime(date('Y-m-d') . "+" . $free_month . " month"));
        $free_days = Carbon::now()->diffInDays(Carbon::parse($expre_date));
        $companyPlan->free_days = $free_days;

        $companyPlan->save();

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        // if registration is closed, deny access
        if (!config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->data['title'] = trans('backpack::base.register'); // set the page title

        return view('backpack::auth.register', $this->data);
    }

    public function showRegisterConfirmationForm()
    {
        // if registration is closed, deny access
        if (!config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->data['title'] = trans('backpack::base.register'); // set the page title

        return view('backpack::auth.register_confirmation', $this->data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // if registration is closed, deny access
        if (!config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->validator($request->all())->validate();

        $data = $request->all();

        $data['registration_code'] = sprintf("%06d", random_int(1, 999999));
        $data['registration_code_expired'] = Carbon::now()->addHours(config('auth.register_code_expired_hours'))->toDateTimeString();

        $this->create($data);

        Mail::to($request->email)->send(new CompanyRegistered($data['registration_code']));

        return redirect(route('backpack.auth.register.confirm'));
    }

    public function registerConfirm(Request $request)
    {
        // if registration is closed, deny access
        if (!config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $validator = Validator::make($request->all(), [
            'registration_code' => [
                'required',
                'exists:users,registration_code',
                function($attribute, $value, $fail) use ($request) {
                    $user = User::where('registration_code', $request->registration_code)
                        ->where('registration_code_expired', '>', Carbon::now()->toDateTimeString())
                        ->first();
                    if(!$user) {
                        return $fail(trans('validation.exists'));
                    }
                }
            ],
        ])->validate();

        $user = User::where('registration_code', $request->registration_code)
            ->where('registration_code_expired', '>', Carbon::now()->toDateTimeString())
            ->first();

        if($user) {
            $user->registration_code = null;
            $user->registration_code_expired = null;
            $user->status = User::STATUS_APPROVED;

            $user->save();

            $this->guard()->login($user);
        }

        return redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
