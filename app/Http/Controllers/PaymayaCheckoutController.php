<?php

namespace App\Http\Controllers;

use Aceraven777\PayMaya\API\Customization;
use Aceraven777\PayMaya\API\VoidPayment;
use Aceraven777\PayMaya\API\Webhook;
use Aceraven777\PayMaya\Model\Checkout\Address;
use App\Mail\ReceiptMail;
use App\Models\Colhdr;
use App\Models\Collne;
use App\Models\Collne4;
use App\Models\EmailDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Aceraven777\PayMaya\PayMayaSDK;
use Aceraven777\PayMaya\API\Checkout;
use Aceraven777\PayMaya\Model\Checkout\Item;
use App\Libraries\PayMaya\User as PayMayaUser;
use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
use App\Models\Checkout as transCheckout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use mysql_xdevapi\Table;

class PaymayaCheckoutController extends Controller
{

    public function setupPayMaya()
    {
        PayMayaSDK::getInstance()->initCheckout(
            env('PAYMAYA_PUBLIC_KEY'),
            env('PAYMAYA_SECRET_KEY'),
            (\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
        );

        $this->setShopCustomization();
        $this->setWebhooks();

        return redirect('/');
    }

    public function redirectToPayMaya(Request $request)
    {
        PayMayaSDK::getInstance()->initCheckout(
            env('PAYMAYA_PUBLIC_KEY'),
            env('PAYMAYA_SECRET_KEY'),
            (\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
        );

        $sample_reference_number = '000000000000001';
        $total = 0;

        $itemArr = array();
        foreach($request->rpt as $data)
        {
            $total += (double)$data['amount'];

            $itemAmountDetails = new ItemAmountDetails();
            $itemAmountDetails->tax = "0.00";
            $itemAmountDetails->subtotal = number_format($data['amount'], 2, '.', '');

            $itemAmount = new ItemAmount();
            $itemAmount->currency = "PHP";
            $itemAmount->value = number_format($data['amount'], 2, '.', '');
            $itemAmount->details = $itemAmountDetails;

            $item = new Item();
            $item->name = $data['bill_num'];
            $item->amount = $itemAmount;
            $item->totalAmount = $itemAmount;

            array_push($itemArr, $item);
        }

        /* get all totals */
        $checkout_id = '';
//        $service_charges = 50.00;

//        $grand_total = $total + $service_charges


        $itemAmountDetails_2 = new ItemAmountDetails();
        $itemAmountDetails_2->tax = "0.00";
        $itemAmountDetails_2->serviceCharge = $request->servicefee;
        $itemAmountDetails_2->subtotal = number_format($request->subtotal, 2, '.', '');

        $itemAmount_2 = new ItemAmount();
        $itemAmount_2->currency = "PHP";
        $itemAmount_2->value = number_format($request->grandtotal, 2, '.', '');
        $itemAmount_2->details = $itemAmountDetails_2;

        /* billing informations */

        $itemCheckout = new Checkout();

        $address = new Address();
        $address->line1 = $request->address;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->zipCode = $request->postal_code;
        $address->countryCode = 'PH';

        $user = new PayMayaUser();
        $user->contact->phone = $request->mobile;
        $user->contact->email = Auth::user()->email;
        $user->firstName = $request->first_name;
        $user->lastName = $request->last_name;
        $user->middleName = $request->middle_name;
        $user->shippingAddress = $address;
        $user->billingAddress = $address;

        $itemCheckout->buyer = $user->buyerInfo();
        $itemCheckout->items = $itemArr;
        $itemCheckout->totalAmount = $itemAmount_2;
        $itemCheckout->requestReferenceNumber = $sample_reference_number;
        $itemCheckout->redirectUrl = array(
            "success" => url('paymaya_success'),
            "failure" => url('paymaya_failed'),
            "cancel" => url('paymaya_cancel'),
        );

        $itemCheckout->execute();
        $itemCheckout->retrieve();

        session(['id' => $itemCheckout->id]);
        return response()->json(['checkout' => $itemCheckout->url], 200);
    }

    private function setShopCustomization()
    {
        $shopCustomization = new Customization();
        $shopCustomization->get();

        $shopCustomization->logoUrl = asset('image/smartservelogo.jpg');
        $shopCustomization->iconUrl = asset('image/smartservelogo.jpg');
        $shopCustomization->appleTouchIconUrl = asset('image/smartservelogo.jpg');
        $shopCustomization->customTitle = 'Pagadian RPT Online Payment';
        $shopCustomization->colorScheme = '#f3dc2a';

        $shopCustomization->set();
    }

    private function setWebhooks()
    {
        $webhooks = Webhook::retrieve();
        foreach ($webhooks as $webhook) {
            $webhook->delete();
        }

        $successWebhook = new Webhook();
        $successWebhook->name = Webhook::CHECKOUT_SUCCESS;
        $successWebhook->callbackUrl = url('https://3bef-136-158-30-102.ngrok.io/pagadian-online-payment/paymaya/success');
        $successWebhook->register();

        $failureWebhook = new Webhook();
        $failureWebhook->name = Webhook::CHECKOUT_FAILURE;
        $failureWebhook->callbackUrl = url('callback/error');
        $failureWebhook->register();
    }

    public function success(Request $request)
    {
        PayMayaSDK::getInstance()->initCheckout(
            env('PAYMAYA_PUBLIC_KEY'),
            env('PAYMAYA_SECRET_KEY'),
            (\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
        );

        $transaction_id = session()->get('id');

        if (! $transaction_id) {
            return ['status' => false, 'message' => 'Transaction Id Missing'];
        }

        $itemCheckout = new Checkout();
        $itemCheckout->id = session()->get('id');
        $checkout = $itemCheckout->retrieve();

        if($checkout['status'] == 'COMPLETED')
        {
            $items = [];

            foreach($checkout['items'] as $item)
            {
                /* check colhdr if the bill number already process */
                $update_checkout = transCheckout::where('bill_num', $item['name'])->where('paid', false)->where('user_id', Auth::user()->id)->first();

                if($update_checkout != null)
                {
                    $update_checkout->paid = true;
                    $update_checkout->payment_date = date('m/d/Y');
                    $update_checkout->transaction_id = $checkout['id'];
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
                        ->where('mp_desc', 'PAYMAYA')
                        ->select('mp_code')
                        ->first();


                    $colhdr = new Colhdr();
                    $colhdr->or_code = $or_code->or_code;
                    $colhdr->trnx_date = date('Y-m-d');
                    $colhdr->full_name = strtoupper($checkout['buyer']['lastName'] . ' ' . $checkout['buyer']['firstName'] . ' ' . $checkout['buyer']['middleName']);
                    $colhdr->or_number = $or_number->next_ornumber;
                    $colhdr->user_id = 'ARNOLD';
                    $colhdr->t_date = date('Y-m-d');
                    $colhdr->t_time = date('h:i');
                    $colhdr->value_date = date('Y-m-d');
                    $colhdr->mobile = $checkout['buyer']['contact']['phone'];
                    $colhdr->coll_type = 2;
                    $colhdr->bill_num = $item['name'];
                    $colhdr->payor = strtoupper($checkout['buyer']['lastName'] . ' ' . $checkout['buyer']['firstName'] . ' ' . $checkout['buyer']['middleName']);
                    $colhdr->save();


                    $collne = new Collne();
                    $collne->or_code = $or_code->or_code;
                    $collne->ln_num= 1;
                    $collne->pay_code = $mp_code->mp_code;
                    $collne->amnt_paid = $item['amount']['value'];
                    $collne->save();


                    $bill_info = DB::table('rpubilllne')->where('bill_num' , $item['name'])->get();
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

                    array_push($items, ['bill_num' => $item['name'], 'amount' => $item['amount']['value']]);
                }

            }

            if($item != null)
            {
                $email_details = collect([
                    'name' => $checkout['buyer']['lastName'] . ' ' . $checkout['buyer']['firstName'] . ' ' . $checkout['buyer']['middleName'],
                    'transaction_date' => date('Y-m-d'),
                    'items' => $items,
                    'total' => $checkout['totalAmount']['amount'],
                    'invoice' => '00000000001234',
                    'method' => 'paymaya',
                    'transaction_fee' => $checkout['totalAmount']['details']['serviceCharge'],
                ]);


                /* send acknowledgement */
                Mail::to(Auth::user()->email)->send(new ReceiptMail($email_details));
            }


            Session::flash('success', 'Payment Success!');
            return redirect()->to('/');
        }

//        try
//        {
//            if($request->session()->get('id') !== null)
//            {
//
//
//                PayMayaSDK::getInstance()->initCheckout(
//                    env('PAYMAYA_PUBLIC_KEY'),
//                    env('PAYMAYA_SECRET_KEY'),
//                    (\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
//                );
//
//                $transaction_id = $request->session()->get('id');
////                if (! $transaction_id) {
////                    return ['status' => false, 'message' => 'Transaction Id Missing'];
////                }
////
//                $itemCheckout = new Checkout();
//                $itemCheckout->id = $transaction_id;
//                $checkout = $itemCheckout->retrieve();
//
//
//
//                if ($checkout)
//                {
////                    $error = $itemCheckout::getError();
////                    dd($error);
////                    Session::flash('error', $error['message']);
////                    return redirect()->to('/')
//                    if($checkout['paymentStatus'] === 'PAYMENT_SUCCESS')
//                    {
//                        $billing_info = collect();
//                        $receipt_no = $checkout['receiptNumber'];
//                        $payment_scheme = $checkout['paymentScheme'];
//                        $mobile = $checkout['buyer']['contact']['phone'];
//                        $service_charge = $checkout['totalAmount']['details']['serviceCharge'];
//                        $transaction_reference_number = $checkout['transactionReferenceNumber'];
//                        $full_name = $checkout['buyer']['lastName'] . ' ' . $checkout['buyer']['firstName'] . ', ' . $checkout['buyer']['middleName'];
//                        $sub_total = $checkout['totalAmount']['details']['subtotal'];
//
//                        foreach($checkout['items'] as $item)
//                        {
//
//                            /* check colhdr if the bill number already process */
//                            $update_checkout = transCheckout::where('bill_num', $item['name'])->where('paid', false)->where('user_id', Auth::user()->id)->first();
//                            $update_checkout->paid = true;
//                            $update_checkout->payment_date = date('m/d/Y');
//                            $update_checkout->transaction_id = $transaction_id;
//                            $update_checkout->save();
//
//                            /* save to treasury database using pagadian_smartserve_database */
//                            DB::connection('pgsql_2')->raw('LOCK TABLES cashiersetup WRITE');
//                            $or_number = DB::connection('pgsql_2')
//                                ->table('cashiersetup')
//                                ->select('next_ornumber')->where('user_id', 'ARNOLD')
//                                ->first();
//
//                            /* save data to colhdr */
//                            DB::connection('pgsql_2')->raw('LOCK TABLES m99 WRITE');
//                            $or_code = DB::connection('pgsql_2')
//                                ->table('m99')
//                                ->select('or_code')
//                                ->first();
//
//
//                            /* get mp_code */
//                            $mp_code = DB::connection('pgsql_2')
//                                ->table('m10')
//                                ->where('online_rpt', true)
//                                ->where('mp_desc', 'PAYMAYA')
//                                ->select('mp_code')
//                                ->first();
//
//
//                            $colhdr = new Colhdr();
//                            $colhdr->or_code = $or_code->or_code;
//                            $colhdr->trnx_date = date('Y-m-d');
//                            $colhdr->full_name = strtoupper($full_name);
//                            $colhdr->or_number = $or_number->next_ornumber;
//                            $colhdr->user_id = 'ARNOLD';
//                            $colhdr->t_date = date('Y-m-d');
//                            $colhdr->t_time = date('h:i');
//                            $colhdr->value_date = date('Y-m-d');
//                            $colhdr->mobile = $mobile;
//                            $colhdr->coll_type = 2;
//                            $colhdr->bill_num = $item['name'];
//                            $colhdr->payor = strtoupper($full_name);
//                            $colhdr->save();
//
//
//                            $collne = new Collne();
//                            $collne->or_code = $or_code->or_code;
//                            $collne->ln_num= 1;
//                            $collne->pay_code = $mp_code->mp_code;
//                            $collne->amnt_paid = $item['amount']['value'];
//                            $collne->save();
//
//
//                            $bill_info = DB::table('rpubilllne')->where('bill_num' , $item['name'])->get();
//                            $ln_num = 1;
//
//                            foreach($bill_info as $bill)
//                            {
//                                $collne4 = new Collne4();
//                                $collne4->or_code = $or_code->or_code;
//                                $collne4->ln_num = $ln_num;
//                                $collne4->rpu_num = $bill->rpu_num;
//                                $collne4->yr1 = $bill->yr1;
//                                $collne4->yr2 = $bill->yr2;
//                                $collne4->tax_paid = $bill->tax_amnt;
//                                $collne4->pen_paid = $bill->penalty;
//                                $collne4->bill_num = $bill->bill_num;
//                                $collne4->sef_paid = $bill->sef_amnt;
//                                $collne4->penalty_sef_paid = $bill->penalty_sef;
//                                $collne4->save();
//                                $ln_num++;
//                            }
//
//                            $new_or_code = str_pad($or_code->or_code + 1, 8, 0, STR_PAD_LEFT);
//                            $new_or_number = str_pad($or_number->next_ornumber + 1, 15, 0, STR_PAD_LEFT);
//
//                            DB::connection('pgsql_2')->table('m99')->update(['or_code' => $new_or_code]);
//                            DB::connection('pgsql_2')->table('cashiersetup')->where('user_id', 'ARNOLD')->update(['next_ornumber' => $new_or_number]);
//
//
//                            $billing_info->push(['bill_num' => $item['name'], 'amount' => $item['amount']]);
//                        }
//
//                        $email_details = collect([
//                            'transaction_date' => date('Y-m-d'),
//                            'items' => $billing_info,
//                            'total' => $sub_total,
//                            'invoice' => '00000000001234',
//                            'method' => 'paymaya',
//                            'transaction_fee' => $service_charge,
//                        ]);
//
//                        /* send acknowledgement */
//                        Mail::to(Auth::user()->email)->send(new ReceiptMail($email_details));
//
//                        Session::flash('success', 'Payment Success!');
//                        return redirect()->to('/');
//                    }
//                }
//            }
//        }
//        catch(\Exception $ex) {
//            Session::flash('error', $ex);
//            return redirect()->to('/');
//        }
    }

    public function failed(Request $request) {
        Session::flash('error', 'Payment Failed!');
        return redirect()->to('/');
    }

    public function chargable(Request $request)
    {
        PayMayaSDK::getInstance()->initCheckout(
            env('PAYMAYA_PUBLIC_KEY'),
            env('PAYMAYA_SECRET_KEY'),
            (\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
        );

        DB::connection('pgsql')->table('barangay')->where('brgy_code', '001')->update(['brgy_desc' => 'Alergy']);

//        $transaction_id = $request->get('id');
//        if (! $transaction_id) {
//            return ['status' => false, 'message' => 'Transaction Id Missing'];
//        }
//
//        $itemCheckout = new Checkout();
//        $itemCheckout->id = $transaction_id;
//
//        $checkout = $itemCheckout->retrieve();
//
//        if ($checkout === false) {
//            $error = $itemCheckout::getError();
//            return redirect()->back()->withErrors(['message' => $error['message']]);
//        }
//
//        $email_details = collect([
//            'transaction_date' => date('Y-m-d'),
//            'items' => 'test',
//            'total' => 'test',
//            'invoice' => '00000000001234',
//            'method' => 'gcash',
//            'transaction_fee' => 'test',
//        ]);
//
//        /* send invoice */
//        Mail::to(Auth::user()->email)->send(new ReceiptMail($email_details));
//
//
//        return $checkout;
    }

}
