<?php

namespace App\Http\Controllers;

use App\Models\Rpt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index () {
        return view('main.home');
    }

    public function initial_data() {

        $rpts = Rpt::leftJoin('rpumaster','rpumaster.id', 'rpts.rpumaster_id')
            ->leftJoin('barangay', 'barangay.brgy_code', 'rpumaster.brgy_code')
            ->where('rpts.user_id', Auth::user()->id)
            ->select('rpumaster.arp', 'rpumaster.pin', 'barangay.brgy_desc')
            ->get();

        $barangay = DB::table('barangay')->select('brgy_code', 'brgy_desc')->orderBy('brgy_desc')->get();

        return response()->json(['data' => $rpts, 'barangay' => $barangay], 200);
    }

    public function store(Request $request) {

        /* check if the apt, tin and barangay is match */
        $check_data = DB::table('rpumaster')
            ->where('arp', $request->arp)
            ->where('pin', $request->pin)
            ->where('brgy_code', $request->barangay)
            ->first();

        if($check_data == null)
        {
            return response()->json(['errors' => 'No record found'], 422);
        }

        $check_online_data = Rpt::leftJoin('rpumaster', 'rpumaster.id', 'rpts.rpumaster_id')
            ->where('rpumaster.arp', $request->arp)
            ->where('rpumaster.pin', $request->pin)
            ->where('rpumaster.brgy_code', $request->barangay)
            ->first();

        if($check_online_data != null)
        {
            return response()->json(['errors' => 'RPT already exist!'], 422);
        }

        $store_data = new Rpt();
        $store_data->rpumaster_id = $check_data->id;
        $store_data->user_id = Auth::user()->id;
        $store_data->brgy_code = $request->barangay;
        return $store_data->save()
            ? response()->json(['message' => 'Data Successfully Added!'], 200)
            : response()->json(['errors' => 'Something when Wrong!'], 422);
    }

    public function destroy($id)
    {
        $data = Rpt::where('id', $id)->first();
        return $data->delete() ? response()->json(['message' => 'Record Successfully Deleted'])
            : response()->json(['errors' => 'Something when Wrong!']);
    }

    public function mark_for_payment($id, $bool) {
        $data = Rpt::where('id', $id)->first();
        $data->for_payment = $bool;
        $data->save();
    }
}
