<?php

namespace App\Http\Controllers;

use App\Mail\ReceiptMail;
use App\Models\Checkout;
use App\Models\Checkout as transCheckout;
use App\Models\Colhdr;
use App\Models\Collne;
use App\Models\Collne4;
use App\Models\Rpt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Luigel\Paymongo\Facades\Paymongo;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index() {
        return view('main.payment');
    }

    public function initial_data()
    {

        $get_bill = DB::table('rpubilllne')
            ->leftJoin('rpumaster', 'rpumaster.rpu_num', 'rpubilllne.rpu_num')
            ->leftJoin('rpubillhdr', 'rpubillhdr.bill_num', 'rpubilllne.bill_num')
            ->leftJoin('rpts', 'rpts.rpumaster_id', 'rpumaster.id')
            ->select('rpubillne.a')
            ->select('rpts.id', 'rpumaster.pin', 'rpumaster.arp', 'rpubilllne.bill_num', 'rpubilllne.ln_amnt', 'rpubilllne.yr1','rpubillhdr.trnx_date')
            ->where('rpts.user_id', Auth::user()->id)
            ->get();

        $grouped_bill = $get_bill->groupBy('bill_num')->each(function($item, $index) {

            $checkout = Checkout::where('bill_num', $index)->where('paid', false)->where('amount', '>' ,0)->first();

            foreach($item as $data)
            {
                $data->w_checkout = (bool)$checkout;
            }
        });

        $checkout_total = 0;

        foreach($grouped_bill as $key=>$value)
        {
            $checkout_data = Checkout::where('bill_num', $key)->where('user_id', Auth::user()->id)->select('amount', 'paid')->first();

            if($checkout_data == null)
            {
                $checkout_total += 0;
            }
            else
            {
                if($checkout_data->paid)
                {
                    unset($grouped_bill[$key]);
                    continue;
                }
                else
                {
                    $checkout_total += $checkout_data->amount;
                }
            }
        }


        return response()->json(['bills' => $grouped_bill, 'checkout_total' => $checkout_total], 200);





//        $bill = Rpt::leftJoin('rpumaster', 'rpumaster.id', 'rpts.rpumaster_id')
//                ->leftJoin('rpubilllne', 'rpubilllne.rpu_num', 'rpumaster.rpu_num')
//                ->leftJoin('checkouts', 'checkouts.rpt_id', 'rpts.id')
//                ->select('rpts.id', 'rpumaster.pin', 'rpumaster.arp', 'rpubilllne.bill_num', DB::raw('SUM(rpubilllne.ln_amnt) as total'), 'checkouts.amount')
//                ->groupBy('checkouts.amount', 'rpts.id','rpumaster.pin', 'rpumaster.arp', 'rpubilllne.bill_num', 'checkouts.amount')
//                ->where('rpts.user_id', Auth::user()->id)
//                ->where('rpubilllne.ln_amnt', '!=', 0)
//                ->orderBy('checkouts.amount')
//                ->get();
//
//        $bill->each(function($item) {
//            /* must check if the data has a existing checkouts */
////            $checkout = Checkout::where('checkouts.rpt_id', $item->id)->where('paid', false)->first();
////            $item->amount = $checkout != null  ? $checkout->amount : "0";
//
//            $item->amount = 0;
//            $item->for_payment = false;
//        });
//
//        $totals = $bill->sum('amount');




//        return response()->json(['data' => $bill, 'totals' => $totals], 200);
    }

    public function add_checkout(Request $request) {

        $if_exist_update = Checkout::where('bill_num', $request->bill_num)->where('paid', false)->first();

        if($if_exist_update === null)
        {
            $checkout = new Checkout();
            $checkout->user_id = Auth::user()->id;
            $checkout->bill_num = $request->bill_num;
            $checkout->amount = $request->amount;
            $checkout->paid = false;
            $checkout->save();
        }
        else
        {
            $if_exist_update->amount = $request->amount;
            $if_exist_update->save();
        }

    }

//    public function card(Request $request) {
//
//        $exp_date = str_split($request->exp_date,2);
//        $paymentIntent = Paymongo::paymentIntent()
//            ->create([
//                'amount' => $request->total,
//                'payment_method_allowed' => [
//                    'paymaya', 'card'  // <--- Make sure to add paymaya here.
//                ],
//                'payment_method_options' => [
//                    'card' => [
//                        'request_three_d_secure' => 'automatic',
//                    ],
//                ],
//                'description' => 'Pagadian RPT Online Transactions',
//                'statement_descriptor' => 'PAGADIAN RPT E PAYMENT',
//                'currency' => 'PHP',
//            ]);
//
//        $data = $paymentIntent->getData();
//        $paymentIntentID = $data['id'];
//
//        $paymentMethod = Paymongo::paymentMethod()
//            ->create([
//                'type' => 'card',
//                    'details' => [
//                        'card_number' => $request->card_no,
//                        'exp_month' => (int)$exp_date[0],
//                        'exp_year' => (int)$exp_date[1],
//                        'cvc' => $request->cvc,
//                    ],
//                    'billing' => [
//                        'address' => [
//                            'line1' => $request->address,
//                            'city' => $request->city,
//                            'state' => $request->state,
//                            'country' => 'PH',
//                            'postal_code' => $request->postal_code,
//                        ],
//                        'name' => $request->fullname,
//                        'email' => 'lesther.greenapple@gmail.com',
//                        'phone' => '09298237509'
//                    ],
//            ]);
//
//        $pmdata = $paymentMethod->getData();
//        $paymentMethodID = $pmdata['id'];
//
//        $pMethod = Paymongo::paymentMethod()->find($paymentMethodID);
//        $pMethodID = $pMethod->getData();
//
//        $pIntent = Paymongo::paymentIntent()->find($paymentIntentID);
//
//        DB::beginTransaction();
//
//        try
//        {
//            $successfulPayment = $pIntent->attach($pMethodID['id']);
//            $pIntentStatus = json_decode(json_encode($successfulPayment->getData()),true);
//            $paymentIntentStatus = $pIntentStatus['status'];
//
//            if ($paymentIntentStatus == 'awaiting_next_action')
//            {
//                // render your modal for 3D Secure Authentication since next_action has a value. You can access the next action via paymentIntent.attributes.next_action.
//                $srcurl = $pIntentStatus['next_action']['redirect']['url'];
//                return view('paymentAction', compact('paymentIntentID', 'srcurl'));
//            }
//            else if ($paymentIntentStatus === 'succeeded')
//            {
//
//
//                /* save all transaction details to colhdr collne collne4 */
//
//                /* email receipt to the client */
//                $transact_date = date('m/d/Y H:i:s');
////                Auth::user()->email
//                Mail::to('lesther.greenapple@gmail.com')->send(new ReceiptMail($request, $transact_date));
//
//                $colhdr = new Colhdr();
//                $colhdr->or_code = '00000001';
//                $colhdr->trnx_date = date('Y-m-d');
//                $colhdr->full_name = $request->fullname;
//                $colhdr->or_number = '000000000000001';
//                $colhdr->user_id = 'ONLINE';
//                $colhdr->t_date = date('Y-m-d');
//                $colhdr->t_time = date('H:i');
//                $colhdr->value_date = date('Y-m-d');
//                $colhdr->mobile = Auth::user()->mobile;
//                $colhdr->coll_type = 1;
//                $colhdr->save();
//
//
////                foreach($request->rpt as $bill)
////                {
////                    $collne4 = new Collne4();
////                    $collne4->or_code = '00000001';
////                    $collne4->ln_num
////                }
//
//
//
//
//            }
//            else if($paymentIntentStatus === 'awaiting_payment_method')
//            {
//                // The PaymentIntent encountered a processing error. You can refer to paymentIntent.attributes.last_payment_error to check the error and render the appropriate error message.
//                return response()->json(['errors' => 'Awaiting Payment Method'],422);
//            }
//            else
//            {
//                return response()->json(['errors' => 'Change your payment method'],422);
//            }
//        }
//        catch (\Throwable $exception)
//        {
//            DB::rollback();
//            return back()->withError($exception->getMessage())->withInput();
//        }
//
//        DB::commit();
//        return response()->json(['message' => 'Success'],200);
//
//    }

    public function gcash(Request $request) {

        $gcashSource = Paymongo::source()->create([
            'type' => 'gcash',
            'amount' => $request->subtotal,
            'currency' => 'PHP',
            'redirect' => [
                'success' => 'http://localhost/pagadian-online-payment/gcash_success',
                'failed' => 'http://localhost/pagadian-online-payment/gcash_failed'
            ],
            'billing' => [
                'address' => [
                    'line1' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'country' => 'PH'
                ],
                'name' => $request->fullname,
                'email' => Auth::user()->email,
                'phone' => $request->mobile
            ],
        ]);

//        $gcashSourceID = $gcashSource->id;

        $redirect_url = $gcashSource->redirect['checkout_url'];

        /* get items */

        $bills = Checkout::where('user_id', Auth::user()->id)->where('paid', false)->select('bill_num', 'amount')->get();

        session(['gcashsource' => $gcashSource, 'bills' => $bills]);
        return response()->json(['checkout' => $redirect_url], 200);

    }

    public function gcash_success(Request $request) {

        $srcid = session()->get('gcashsource')->id;
        $srctype = session()->get('gcashsource')->type;
        $amount = session()->get('gcashsource')->amount;
        $bills = session()->get('bills');

        try {
            $payment = Paymongo::payment()
                ->create([
                    'amount' => $amount,
                    'currency' => 'PHP',
                    'description' => 'Testing GCash Payment',
                    'statement_descriptor' => 'Test Gcash via Paymongo',
                    'source' => [
                        'id' => $srcid,
                        'type' => $srctype
                    ]
                ]);

//            $receipt_no = $payment['receiptNumber'];
//            $payment_scheme = $payment['paymentScheme'];
            $mobile = $payment->billing['phone'];
//            $service_charge = $payment['totalAmount']['details']['serviceCharge'];
//            $transaction_reference_number = $payment['transactionReferenceNumber'];
            $full_name = $payment->billing['name'];


//            $sub_total = $payment['totalAmount']['details']['subtotal'];

            foreach($bills as $item)
            {
                $update_checkout = transCheckout::where('bill_num', $item->bill_num)->where('paid', false)->where('user_id', Auth::user()->id)->first();
                $update_checkout->paid = true;
                $update_checkout->payment_date = date('m/d/Y');
                $update_checkout->save();

                /* save to treasury database using pagadian_smartserve_database */

                DB::connection('pgsql_2')->raw('LOCK TABLES cashiersetup WRITE');
                $or_number = DB::connection('pgsql_2')
                    ->table('cashiersetup')
                    ->select('next_ornumber')->where('user_id', 'ARNOLD')
                    ->first();


                /* save data to colhdr */

                DB::connection('pgsql_2')->raw('LOCK TABLES m99 WRITE');
                $or_code = DB::connection('pgsql_2')
                    ->table('m99')
                    ->select('or_code')
                    ->first();


                /* get mp_code */
                $mp_code = DB::connection('pgsql_2')
                    ->table('m10')
                    ->where('online_rpt', true)
                    ->select('mp_code')
                    ->first();

                $colhdr = new Colhdr();
                $colhdr->or_code = $or_code->or_code;
                $colhdr->trnx_date = date('Y-m-d');
                $colhdr->full_name = $full_name;
                $colhdr->or_number = $or_number->next_ornumber;
                $colhdr->user_id = 'ARNOLD';
                $colhdr->t_date = date('Y-m-d');
                $colhdr->t_time = date('h:i');
                $colhdr->value_date = date('Y-m-d');
                $colhdr->mobile = $mobile;
                $colhdr->coll_type = 2;
                $colhdr->bill_num = $item->bill_num;
                $colhdr->payor = $full_name;
                $colhdr->save();

                $collne = new Collne();
                $collne->or_code = $or_code->or_code;
                $collne->ln_num= 1;
                $collne->pay_code = $mp_code->mp_code;
                $collne->amnt_paid = $item->amount;
                $collne->save();

                $bill_info = DB::table('rpubilllne')->where('bill_num' , $item->bill_num)->get();
                $ln_num = 1;

                foreach($bill_info as $bill)
                {
                    $collne4 = new Collne4();
                    $collne4->or_code = $or_code->or_code;
                    $collne4->ln_num = $ln_num;
                    $collne4->rpu_num = $bill->rpu_num;
                    $collne4->yr1 = $bill->yr1;
                    $collne4->yr2 = $bill->yr2;
                    $collne4->tax_paid = $bill->tax_amnt;
                    $collne4->pen_paid = $bill->penalty;
                    $collne4->bill_num = $bill->bill_num;
                    $collne4->sef_paid = $bill->sef_amnt;
                    $collne4->penalty_sef_paid = $bill->penalty_sef;
                    $collne4->save();
                    $ln_num++;
                }

                $new_or_code = str_pad($or_code->or_code + 1, 8, 0, STR_PAD_LEFT);
                $new_or_number = str_pad($or_number->next_ornumber + 1, 15, 0, STR_PAD_LEFT);

                DB::connection('pgsql_2')->table('m99')->update(['or_code' => $new_or_code]);
                DB::connection('pgsql_2')->table('cashiersetup')->where('user_id', 'ARNOLD')->update(['next_ornumber' => $new_or_number]);

            }

        } catch (\Throwable $e) {
            return $e;
        }

        return redirect()->to('/');

    }

    public function gcash_failed() {
        return 'failed e';
    }

    public function receipt_email() {
//        return view('emails.')
    }
}
