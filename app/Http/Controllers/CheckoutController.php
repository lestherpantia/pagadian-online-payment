<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index() {
        return view('main.checkout');
    }

    public function initial_data() {

        $checkout = Checkout::where('user_id', Auth::user()->id)
            ->where('paid', false)
            ->select('bill_num', 'amount')
            ->get();

        $sub_total = 0;
//        $paymaya_service_charge = 0.03;
        $service_charge = 0;
        $grand_total = 0;

        foreach($checkout as $data) {
            $sub_total += $data->amount;
        }
//
//        $service_charge = ($paymaya_service_charge * $sub_total) + 10;

        $grand_total = $sub_total + $service_charge;

       return response()->json([
           'checkout' => $checkout,
           'sub_total' => $sub_total,
           'service_charge' => $service_charge,
           'grand_total' => $grand_total], 200);

    }
}
