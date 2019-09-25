<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function index()
    {
        $title = trans('fields.financial_summary');

        $startDate = Carbon::now()->startOfYear()->format('Y-m-d h:i:s');
        $endDate = Carbon::now()->endOfYear()->format('Y-m-d h:i:s');

        $start_of_month = Carbon::now()->startOfMonth()->format('Y-m-d h:i:s');
        $end_of_month = Carbon::now()->endOfMonth()->format('Y-m-d h:i:s');

        $start_of_week = Carbon::now()->startOfWeek()->format('Y-m-d h:i:s');
        $end_of_week = Carbon::now()->endOfWeek()->format('Y-m-d h:i:s');

        $start_of_day = Carbon::now()->startOfDay()->format('Y-m-d h:i:s');
        $end_of_day = Carbon::now()->endOfDay()->format('Y-m-d h:i:s');

        $year_items = PaymentTransaction::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get()->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('m');
        })->toArray();

        $year_items_paypal = PaymentTransaction::where('created_at', '>=', $startDate)->where('payment_type', 'paypal')->where('created_at', '<=', $endDate)->count();
        $year_items_visa = PaymentTransaction::where('created_at', '>=', $startDate)->where('payment_type', 'visa')->where('created_at', '<=', $endDate)->count();

        $transaction_year = $year_items_paypal + $year_items_visa;
        $transaction_month = PaymentTransaction::where('created_at', '>=', $start_of_month)->where('created_at', '<=', $end_of_month)->count();
        $transaction_week = PaymentTransaction::where('created_at', '>=', $start_of_week)->where('created_at', '<=', $end_of_week)->count();
        $transaction_day = PaymentTransaction::where('created_at', '>=', $start_of_day)->where('created_at', '<=', $end_of_day)->count();


        $data = [
            'title' => $title,
            'transaction_item' => $year_items,
            'visa_num' => $year_items_visa,
            'paypal_num' => $year_items_paypal,
            'year_transaction' => $transaction_year,
            'month_transaction' => $transaction_month,
            'week_transaction' => $transaction_week,
            'day_transaction' => $transaction_day,
        ];

        return view('admin.financial_dashboard', $data);
    }
}
