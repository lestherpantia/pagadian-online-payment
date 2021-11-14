<?php

namespace App\Http\Controllers;

use App\Mail\ReceiptMail;
use App\Models\Colhdr;
use App\Models\Collne4;
use App\Models\Rpt;
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
        $bill = Rpt::leftJoin('rpumaster', 'rpumaster.id', 'rpts.rpumaster_id')
                ->leftJoin('rpubilllne', 'rpubilllne.rpu_num', 'rpumaster.rpu_num')
                ->select('rpumaster.pin', 'rpumaster.arp', 'rpubilllne.bill_num', DB::raw('SUM(rpubilllne.ln_amnt) as total'))
                ->groupBy('rpumaster.pin', 'rpumaster.arp', 'rpubilllne.bill_num')
                ->where('rpts.user_id', Auth::user()->id)
                ->where('rpubilllne.ln_amnt', '!=', 0)
                ->get()
                ->each(function($data) {
                    $data['for_payment'] = false;
                    $data['amount_to_pay'] = 0;
                })->toArray();

        return response()->json(['data' => collect((object)$bill)], 200);
    }

    public function card(Request $request) {

        $exp_date = str_split($request->exp_date,2);
        $paymentIntent = Paymongo::paymentIntent()
            ->create([
                'amount' => $request->total,
                'payment_method_allowed' => [
                    'paymaya', 'card'  // <--- Make sure to add paymaya here.
                ],
                'payment_method_options' => [
                    'card' => [
                        'request_three_d_secure' => 'automatic',
                    ],
                ],
                'description' => 'Pagadian RPT Online Transactions',
                'statement_descriptor' => 'PAGADIAN RPT E PAYMENT',
                'currency' => 'PHP',
            ]);

        $data = $paymentIntent->getData();
        $paymentIntentID = $data['id'];

        $paymentMethod = Paymongo::paymentMethod()
            ->create([
                'type' => 'card',
                    'details' => [
                        'card_number' => $request->card_no,
                        'exp_month' => (int)$exp_date[0],
                        'exp_year' => (int)$exp_date[1],
                        'cvc' => $request->cvc,
                    ],
                    'billing' => [
                        'address' => [
                            'line1' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'country' => 'PH',
                            'postal_code' => $request->postal_code,
                        ],
                        'name' => $request->fullname,
                        'email' => 'lesther.greenapple@gmail.com',
                        'phone' => '09298237509'
                    ],
            ]);

        $pmdata = $paymentMethod->getData();
        $paymentMethodID = $pmdata['id'];

        $pMethod = Paymongo::paymentMethod()->find($paymentMethodID);
        $pMethodID = $pMethod->getData();

        $pIntent = Paymongo::paymentIntent()->find($paymentIntentID);

        DB::beginTransaction();

        try
        {
            $successfulPayment = $pIntent->attach($pMethodID['id']);
            $pIntentStatus = json_decode(json_encode($successfulPayment->getData()),true);
            $paymentIntentStatus = $pIntentStatus['status'];

            if ($paymentIntentStatus == 'awaiting_next_action')
            {
                // render your modal for 3D Secure Authentication since next_action has a value. You can access the next action via paymentIntent.attributes.next_action.
                $srcurl = $pIntentStatus['next_action']['redirect']['url'];
                return view('paymentAction', compact('paymentIntentID', 'srcurl'));
            }
            else if ($paymentIntentStatus === 'succeeded')
            {


                /* save all transaction details to colhdr collne collne4 */

                /* email receipt to the client */
                $transact_date = date('m/d/Y H:i:s');
//                Auth::user()->email
                Mail::to('lesther.greenapple@gmail.com')->send(new ReceiptMail($request, $transact_date));

                $colhdr = new Colhdr();
                $colhdr->or_code = '00000001';
                $colhdr->trnx_date = date('Y-m-d');
                $colhdr->full_name = $request->fullname;
                $colhdr->or_number = '000000000000001';
                $colhdr->user_id = 'ONLINE';
                $colhdr->t_date = date('Y-m-d');
                $colhdr->t_time = date('H:i');
                $colhdr->value_date = date('Y-m-d');
                $colhdr->mobile = Auth::user()->mobile;
                $colhdr->coll_type = 1;
                $colhdr->save();


//                foreach($request->rpt as $bill)
//                {
//                    $collne4 = new Collne4();
//                    $collne4->or_code = '00000001';
//                    $collne4->ln_num
//                }




            }
            else if($paymentIntentStatus === 'awaiting_payment_method')
            {
                // The PaymentIntent encountered a processing error. You can refer to paymentIntent.attributes.last_payment_error to check the error and render the appropriate error message.
                return response()->json(['errors' => 'Awaiting Payment Method'],422);
            }
            else
            {
                return response()->json(['errors' => 'Change your payment method'],422);
            }
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            return back()->withError($exception->getMessage())->withInput();
        }

        DB::commit();
        return response()->json(['message' => 'Success'],200);

    }

    public function gcash(Request $request) {


        $gcashSource = Paymongo::source()->create([
            'type' => 'gcash',
            'amount' => 100,
            'currency' => 'PHP',
            'redirect' => [
                'success' => 'http://localhost/pagadian-online-payment/gcash_success',
                'failed' => 'http://localhost/pagadian-online-payment/gcash_failed'
            ],
            'billing' => [
                'address' => [
                    'line1' => 'Test',
                    'city' => 'test City',
                    'postal_code' => '1209',
                    'country' => 'PH'
                ],
                'name' => 'Lesther',
                'email' => 'lesther.greenapple@gmail.com',
                'phone' => '09298237509'
            ],
        ]);

        return response()->json(['gcash_checkout_url' => $gcashSource->redirect['checkout_url']]);

//        $gcashSourceID = $gcashSource->id;

//        return dd($gcashSource);
//        $redirect_url = $gcashSource->redirect['checkout_url'];
//
//        return response()->json(['check_out' => ])
//
//        return Redirect::to($redirect_url)->with(['gcashsource' => $gcashSource]);
    }

    public function gcash_success(Request $request) {



        dd(url('path'));

        $srcid = $request->id;


//        $pSource = Paymongo::source()->getId();

        $srctype = session()->get('gcashsource')->type;
        $amount = session()->get('gcashsource')->amount;


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
            return 'Successful';
        } catch (\Throwable $e) {
            return $e;
        }
//        return 'Success';
    }

    public function gcash_failed() {
        return 'failed e';
    }

    public function receipt_email() {
//        return view('emails.')
    }
}
