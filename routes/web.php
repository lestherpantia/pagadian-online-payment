<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymayaCheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RptController;
use App\Mail\ReceiptMail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['verify' => true]);

/* FOR EMAIL VERIFICATION */

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/* VERIFIED TRUE AND IF USER IS LOGGED IN USE THIS ROUTES */

Route::get('/home', [RptController::class, 'index']);
Route::get('/', [RptController::class, 'index']);
Route::get('profile', [RptController::class, 'index']);
Route::get('profile/initial_data', [RptController::class, 'initial_data']);
Route::post('profile/store', [RptController::class, 'store']);
Route::get('profile/destroy/{id}', [RptController::class, 'destroy']);

/* RPT SELECTION PAYMENT */
Route::get('payment', [PaymentController::class, 'index']);
Route::get('payment/initial_data', [PaymentController::class, 'initial_data']);
Route::post('payment/add_checkout', [PaymentController::class, 'add_checkout']);

/* CHECKOUT */
Route::get('checkout', [CheckoutController::class, 'index']);
Route::get('checkout/initia_data', [CheckoutController::class, 'initial_data']);



Route::post('card_payment', [Paymentcontroller::class, 'card']);
Route::post('gcash', [Paymentcontroller::class, 'gcash']);
Route::get('gcash_success', [Paymentcontroller::class, 'gcash_success']);
Route::get('gcash_failed', [PaymentController::class, 'gcash_failed']);

/* rpt */
Route::get('rpt', [RptController::class, 'index']);

/* Log out */
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('email_test', function () {
    return new ReceiptMail();
});


/* Paymaya Checkout Test */
Route::get('setup', [PaymayaCheckoutController::class, 'setupPayMaya']);
Route::post('paymaya_checkout', [PaymayaCheckoutController::class, 'redirectToPayMaya']);
Route::get('paymaya_success', [PaymayaCheckoutController::class, 'success']);
Route::get('paymaya_failed', [PaymayaCheckoutController::class, 'failed']);
Route::get('success', [PaymayaCheckoutController::class, 'success']);

