<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Currency;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Prologue\Alerts\Facades\Alert;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.home');
    }

    public function register ()
    {
        return view('frontend.register');
    }

    public function login()
    {
        return view('frontend.login');
    }

    public function addPlan(Request $request)
    {
        $id = $request->id;
        $type = $request->type;

        Cookie::queue("biodactil_plan", $id . "-" . $type);
        Alert::success('Please login first')->flash();
        return redirect()->to('register');
    }
}
