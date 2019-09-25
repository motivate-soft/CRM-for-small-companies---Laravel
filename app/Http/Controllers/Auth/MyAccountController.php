<?php

namespace App\Http\Controllers\Auth;

use App\Models\CompanyCurrency;
use Illuminate\Support\Facades\Storage;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountInfoRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class MyAccountController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change his personal information.
     */
    public function getAccountInfoForm()
    {
        $this->data = [
            'title' => trans('backpack::base.my_account'),
            'user' => $this->guard()->user()
        ];
        return view('backpack::auth.account.index', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(AccountInfoRequest $request)
    {

        $result = $this->guard()->user()->update($request->except(['_token']));

        if(backpack_user()->role == \App\User::ROLE_EMPLOYEE) {
            $employee_updates = $request->except(['_token', 'access_company_token']);
            if ($request->file('photo_file')) {
                $path = Storage::disk('uploads')->put('employees/' . $this->guard()->user()->employee->id, $request->file('photo_file'));
                $employee_updates['photo'] = str_replace('/uploads', '', $path);
            }
            $this->guard()->user()->employee->update($employee_updates);
        }

        if(backpack_user()->role == \App\User::ROLE_COMPANY) {
            $company_updates = $request->except(['_token', 'access_company_token', 'currency']);

            if ($request->file('signatory_file')) {
                $path = Storage::disk('uploads')->put('signatures/' . $this->guard()->user()->company->id, $request->file('signatory_file'));
                $company_updates['signatory'] = $path;
            }

            $this->guard()->user()->company->update($company_updates);
        }

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Show the user a form to change his login password.
     */
    public function getChangePasswordForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.change_password', $this->data);
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
