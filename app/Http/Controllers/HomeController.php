<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index() {
        return view('main.home');
    }

    public function initialdata() {
        return response()->json(['username' => Auth::user()->name]);
    }

    public function search($pin, $arp) {

        $arr = array(
            ['id' => 1, 'pin' => 'PIN1234', 'arp' => 'ARP1234', 'amount' => '200'],
            ['id' => 2, 'pin' => 'PIN2345', 'arp' => 'ARP2345', 'amount' => '800'],
            ['id' => 3, 'pin' => 'PIN3456', 'arp' => 'ARP3456', 'amount' => '300'],
            ['id' => 4, 'pin' => 'PIN4567', 'arp' => 'ARP4567', 'amount' => '100'],
            ['id' => 5, 'pin' => 'PIN5678', 'arp' => 'ARP5678', 'amount' => '500'],
            ['id' => 6, 'pin' => 'PIN6789', 'arp' => 'ARP6789', 'amount' => '400']
        );

        $match = false;

        foreach($arr as $key => $value)
        {
            if($value['pin'] == strtoupper($pin) && $value['arp'] == strtoupper($arp))
            {
                return response()->json(['details' => $arr[$key]], 200);
            }
        }

        if($match == false)
        {
            return response()->json(['error' => 'Details not Exist!'], 401);
        }

    }

    public function get_checkout_details(Request $request) {

    }
}
