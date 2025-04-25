<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'upi'], function() {
    Route::any('update', 'Api\UpiController@upicallback');
    Route::any('vyapar/update', 'CallbackController@bvyaper');
    Route::post('generateQr', 'Api\UpiController@QrIntent');
    Route::post('generateQrV2', 'Api\UpiController@QrIntentV2');
    Route::post('statusCheck', 'Api\UpiController@statusCheck');
    Route::post('statusCheckWeb', 'Api\UpiController@statusCheckWeb');
});


Route::group(['prefix' => 'van'], function() {
    Route::any('createVan', 'Api\VanController@createVan');
});

Route::group(['prefix' => 'vyapar'], function() {
   Route::any('dynamicqr', 'Api\HdfcController@QrIntent');
   Route::any('statusCheck', 'Api\HdfcController@statusCheck');
});

Route::group(['prefix' => 'smartpay'], function() {
    Route::any('update', 'Api\PayoutController@smartpaycallback');
    Route::post('transaction', 'Api\PayoutController@transaction');
    Route::post('transactionStatus', 'Api\PayoutController@txnStatusCheck');
});

Route::group(['prefix' => 'smartpay/v2'], function() {
    //Route::any('update', 'Api\PayoutController@smartpaycallback')->middleware('transactionlog:smartpatcallback');
    Route::post('transaction', 'Api\PayoutController@smartpaytransaction');
    Route::post('transactionStatus', 'Api\PayoutController@txnStatusCheck');
});

Route::group(['prefix' => 'balancecheck'], function() {
    Route::any('check/sm/neelanjal/{id}', 'Android\UserController@checkBalance');
    
});

Route::group(['prefix'=> 'callback/update'], function() {
    Route::any('{api}', 'CallbackController@callback');
});

Route::group(['prefix' => 'checkaeps'], function() {
    Route::any('icici/initiate', 'AepsController@iciciaepslog')->middleware('transactionlog:aeps');
    Route::any('icici/update', 'AepsController@iciciaepslogupdate')->middleware('transactionlog:aepsupdate');
});

Route::any('upi/callback', 'UpiController@Upicallback');
Route::any('upi/unpe/callback', 'UpiController@UpiUnpecallback');
Route::any('upi/safepay/callback', 'CallbackController@safepayCallback');
Route::any('upi/sfpay/callback', 'CallbackController@sfpayCallback');

Route::any('upi/sprint/callback', 'CallbackController@sprintCallback');
Route::any('upi/sprint/callback/sprintCallbackdecode', 'CallbackController@sprintCallbackdecode');


Route::any('upi/sprint/callback/checkLoad', 'CallbackController@checkLoad');

Route::any('upi/sprint/callback/disputeRaise', 'CommonController@disputeRaise');

//Route::any('payout/blinkpe/callback', 'UpiController@UpiUnpecallback');
Route::any('getbal/{token}', 'Api\ApiController@getbalance');
Route::any('getip', 'Api\ApiController@getip');

/*Recharge Api*/
Route::any('getprovider', 'Api\RechargeController@getProvider');
Route::any('recharge/pay', 'Api\RechargeController@payment')->middleware('transactionlog:recharge');
Route::any('recharge/status', 'Api\RechargeController@status');

/*Android App Apis*/
Route::any('android/auth/user/register', 'Android\UserController@registration');
Route::any('android/auth', 'Android\UserController@login');
Route::any('android/auth/logout', 'Android\UserController@logout');
Route::any('android/auth/reset/request', 'Android\UserController@passwordResetRequest');
Route::any('android/auth/reset', 'Android\UserController@passwordReset');
Route::any('android/auth/password/change', 'Android\UserController@changepassword');
Route::any('android/auth/user/getactive', 'Android\UserController@getactive');

// Profile Android 
Route::any('android/getstate', 'Android\UserController@getState');
Route::any('android/auth/profile/change', 'Android\UserController@changeProfile');

Route::any('android/getbalance', 'Android\UserController@getbalance');
Route::any('android/aeps/initiate', 'Android\UserController@aepsInitiate')->middleware('transactionlog:aeps');
Route::any('android/aeps/status', 'Android\UserController@aepsStatus');
Route::any('android/secure/microatm/initiate', 'Android\UserController@microatmInitiate')->middleware('transactionlog:microatm');
Route::any('android/secure/microatm/update', 'Android\UserController@microatmUpdate');

Route::any('android/transaction', 'Android\TransactionController@transaction');
Route::any('android/fundrequest', 'Android\FundController@transaction')->middleware('transactionlog:fund');
Route::any('android/tpin/getotp', 'Android\UserController@getotp');
Route::any('android/tpin/generate', 'Android\UserController@setpin');

/*Recharge Android Api*/

Route::any('android/recharge/providers', 'Android\RechargeController@providersList');
Route::any('android/recharge/pay', 'Android\RechargeController@transaction')->middleware('transactionlog:recharge');
Route::any('android/recharge/status', 'Android\RechargeController@status');
Route::any('android/transaction/status', 'Android\TransactionController@transactionStatus')->middleware('transactionlog:transtatus');
Route::any('android/recharge/getplan', 'Android\RechargeController@getplan');

/*Bill Android Api*/

Route::any('android/billpay/providers', 'Android\BillpayController@providersList');
Route::any('android/billpay/getprovider', 'Android\BillpayController@getprovider');
Route::any('android/billpay/transaction', 'Android\BillpayController@transaction')->middleware('transactionlog:billpay');
Route::any('android/billpay/status', 'Android\BillpayController@status');

/*Bill Android Api*/

Route::any('android/pancard/transaction', 'Android\PancardController@transaction')->middleware('transactionlog:pancard');
Route::any('android/pancard/status', 'Android\PancardController@status');

/*Bill Android Api*/

Route::any('android/dmt/transaction', 'Android\MoneyController@transaction')->middleware('transactionlog:dmt');

/*Member Create Android Api*/
Route::any('android/member/create', 'Android\UserController@addMember');
Route::any('android/member/idstock', 'Android\UserController@idStock');
Route::any('android/member/list', 'Android\TransactionController@transaction');


Route::any('android/aepsregistration', 'Android\UserController@aepskyc');
Route::any('android/GetState', 'Android\UserController@GetState');
Route::any('android/GetDistrictByState', 'Android\UserController@GetDistrictByState');

Route::any('statusCheck', 'Api\CosmosUpiController@statusCheck');



Route::any('upi/checkCron', 'CronController@checkSafepayOrder');
Route::any('upi/safepayUpiUpdate', 'CronController@safepayUpiUpdate');
Route::any('upi/sprintUpiUpdate', 'CronController@sprintUpiUpdate');
Route::any('payout/safepayPayoutUpdate', 'CronController@safepayPayoutUpdate');
Route::any('payout/failedPayout', 'CronController@failedPayout');
Route::any('upi/sprintUpiUpdatelattest', 'CronController@sprintUpiUpdatelattest');
Route::any('upi/sprintUpioneByone/{txnid}', 'CronController@sprintUpioneByone');

// RBL UAT 
Route::any('upi/sprintRbl/{type}', 'UserController@paySprintRBLuat');

Route::group(['prefix' => 'cosmosUat'], function() {
    Route::any('upi/verifyVPA', 'CosmosUpiController@verifyVPA');
    Route::any('upi/transferUpi', 'CosmosUpiController@upiTransfer');
    Route::any('upi/txnStatus', 'CosmosUpiController@txnStatus');
    Route::any('upi/upiReport', 'CosmosUpiController@upiReport');
    Route::any('upi/dQr', 'CosmosUpiController@dQr');
    Route::any('upi/callbackDecrypt', 'CosmosUpiController@callbackDecrypt');
    Route::any('upi/qrStatusRRN', 'CosmosUpiController@qrStatusRRN');
    Route::any('upi/qrStatus', 'CosmosUpiController@qrStatus');
    Route::any('upi/qrReport', 'CosmosUpiController@qrReport');
});

Route::any('sabpaisa', 'Android\UserController@sabPaisa');

Route::group(['prefix' => 'callback/upi'], function() {
    Route::any('update', 'CallbackController@upicallback');
});


Route::group(['prefix' => 'p2p/upiSdk'], function() {
    Route::post('initiate', 'Api\P2pController@InitiateRequest');
    Route::post('updateRequest', 'Api\P2pController@updateRequest');
    Route::post('sdkVerify', 'Api\P2pController@sdkVerify');
});