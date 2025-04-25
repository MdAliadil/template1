<?php

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

Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('mylogin');

Route::get('/LoginNew', function () {
    return view('welcome');
})->middleware('guest');


Route::get('/superadmin', function () {
    return view('welcomeadmin');
})->middleware('guest');

Route::get('dashboard/home', [HomeController::class, 'index'])->name('dashboard.home');



Route::get('postdata', 'UserController@postdata');
 Route::get('regis', 'UserController@registerpage')->name('regis');
 Route::get('service', 'UserController@servicepage')->name('service');
 Route::get('contact', 'UserController@contactpage')->name('contact');

Route::get('/privecy-policy', function () {
    return view('privecy-policy');
});


Route::group(['prefix' => 'test/virshan/callback/upi'], function() {
    Route::any('update', 'CallbackController@upicallback')->middleware('transactionlog:newupicallback');
});


Route::get('getotps/{id?}', 'P2pController@getotps')->name('getotps');
Route::group(['prefix' => 'p2p', 'middleware' => ['auth', 'company']], function() {
    
    Route::get('{type}/{id?}', 'P2pController@index')->name('p2psetup');
    //Route::post('update', 'P2pController@update')->name('p2pupdate');
});


Route::group(['prefix' => 'auth'], function() {
    Route::post('check', 'UserController@login')->name('authCheck');
    Route::post('admincheck', 'UserController@adminLogin')->name('adminLogin');
    Route::get('logout', 'UserController@logout')->name('logout');
    Route::post('reset', 'UserController@passwordReset')->name('authReset');
    Route::post('register', 'UserController@registration')->name('register');
    Route::post('getotp', 'UserController@getotp')->name('getotp');
    Route::post('setpin', 'UserController@setpin')->name('setpin');
});


Route::group(['prefix' => 'chargeback', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'ChargebackController@index')->name('chargeback');
    Route::post('userBalance', 'ChargebackController@userBalance')->name('userBalance');
    Route::post('getChargebackHistory', 'ChargebackController@getChargebackHistory')->name('getChargebackHistory');
    Route::post('update', 'ChargebackController@update')->name('chargebackupdate')->middleware('activity');
    Route::post('uploadchargebackupdate', 'ChargebackController@uploadchargebackupdate')->name('uploadchargebackupdate')->middleware('activity');
});


Route::get('/dashboard', 'HomeController@index')->name('home');

Route::post('wallet/balance', 'HomeController@getbalance')->name('getbalance');
Route::get('setpermissions', 'HomeController@setpermissions');
Route::get('setscheme', 'HomeController@setscheme');
Route::get('checkcommission', 'HomeController@checkcommission');
Route::get('getmyip', 'HomeController@getmysendip');
Route::get('balance', 'HomeController@getbalance')->name('getbalance');
Route::any('getdatas', 'HomeController@getdatas')->name('getdatas');
Route::any('getfiltreddatas', 'HomeController@getfiltreddatas')->name('getfiltreddatas');
Route::get('mydata', 'HomeController@mydata');
Route::get('bulkSms', 'HomeController@mydata');

Route::get('initiatedUpi', 'HomeController@getUpiOrders')->name('initiatedUpi');
Route::get('getUpiOrdersSuccess', 'HomeController@getUpiOrdersSuccess')->name('getUpiOrdersSuccess');




Route::any('order/{orderId}', 'Api\UpiController@orderIdInitiate');

Route::group(['prefix'=> 'tools', 'middleware' => ['auth', 'company','checkrole:admin']], function() {
    Route::get('{type}', 'RoleController@index')->name('tools');
    Route::post('{type}/store', 'RoleController@store')->name('toolsstore');
    Route::post('setpermissions','RoleController@assignPermissions')->name('toolssetpermission');
    Route::post('get/permission/{id}', 'RoleController@getpermissions')->name('permissions');
    Route::post('getdefault/permission/{id}', 'RoleController@getdefaultpermissions')->name('defaultpermissions');
});

Route::group(['prefix' => 'statement', 'middleware' => ['auth']], function() {    
    Route::get("export/{type}", 'StatementController@export')->name('export');
    Route::get('{type}/{id?}/{status?}', 'StatementController@index')->name('statement');
    Route::post('fetch/{type}/{id?}/{returntype?}', 'CommonController@fetchData');
    Route::post('update', 'CommonController@update')->name('statementUpdate')->middleware('activity');
    Route::post('status', 'CommonController@status')->name('statementStatus');
    Route::post('resendCallback', 'CommonController@resendCallback')->name('resendCallback');
    Route::post('chargeBackUpdate', 'CommonController@chargeBackUpdate')->name('chargeBackUpdate');
});

Route::group(['prefix'=> 'member', 'middleware' => ['auth', 'company']], function() {
	Route::get('{type}/{action?}', 'MemberController@index')->name('member');
    Route::post('store', 'MemberController@create')->name('memberstore');
    Route::post('commission/update', 'MemberController@commissionUpdate')->name('commissionUpdate')->middleware('activity');
    Route::post('getcommission', 'MemberController@getCommission')->name('getMemberCommission');
    Route::post('getpackagecommission', 'MemberController@getPackageCommission')->name('getMemberPackageCommission');
});

Route::group(['prefix'=> 'portal', 'middleware' => ['auth', 'company']], function() {
	Route::get('{type}', 'PortalController@index')->name('portal');
    Route::post('store', 'PortalController@create')->name('portalstore');
});

Route::group(['prefix'=> 'fund', 'middleware' => ['company']], function() {
    Route::post('sabpaisa', 'FundController@sabPaisa')->name('sabPaisa');
    Route::any('sabpaisa/updateData', 'FundController@sabpaisaupdateData')->name('sabpaisaupdateData');
	Route::get('{type}/{action?}', 'FundController@index')->name('fund');
    Route::post('transaction', 'FundController@transaction')->name('fundtransaction')->middleware('transactionlog:fund');
});



Route::group(['prefix' => 'profile', 'middleware' => ['auth']], function() {
    Route::get('/view/{id?}', 'SettingController@index')->name('profile');
    Route::get('certificate', 'SettingController@certificate')->name('certificate');
    Route::post('update', 'SettingController@profileUpdate')->name('profileUpdate')->middleware('activity');;
});

Route::group(['prefix' => 'setup', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'SetupController@index')->name('setup');
    Route::post('update', 'SetupController@update')->name('setupupdate')->middleware('activity');;
});

Route::group(['prefix' => 'resources', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'ResourceController@index')->name('resource');
    Route::post('update', 'ResourceController@update')->name('resourceupdate')->middleware('activity');;
    Route::post('get/{type}/commission', 'ResourceController@getCommission');
    Route::post('get/{type}/packagecommission', 'ResourceController@getPackageCommission');
});

Route::group(['prefix' => 'recharge', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'RechargeController@index')->name('recharge');
    Route::get('bbps/{type}', 'BillpayController@bbps')->name('bbps');
    Route::post('payment', 'RechargeController@payment')->name('rechargepay')->middleware('activity');
    Route::post('getplan', 'RechargeController@getplan')->name('getplan');
});

Route::group(['prefix' => 'billpay', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'BillpayController@index')->name('bill');
    Route::post('payment', 'BillpayController@payment')->name('billpay')->middleware('transactionlog:billpay');
    Route::post('getprovider', 'BillpayController@getprovider')->name('getprovider');
});

Route::group(['prefix' => 'pancard', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'PancardController@index')->name('pancard');
    Route::post('payment', 'PancardController@payment')->name('pancardpay')->middleware('transactionlog:pancard');
     Route::get('nsdl/view/{id}','PancardController@nsdlview');
});

Route::group(['prefix' => 'dmt', 'middleware' => ['auth', 'company']], function() {
    Route::get('/', 'DmtController@index')->name('dmt1');
    //Route::post('transaction', 'DmtController@payment')->name('dmt1pay')-->middleware('transactionlog:dmt');
});

Route::group(['prefix' => 'aeps', 'middleware' => ['auth', 'company']], function() {
    Route::get('/', 'AepsController@index')->name('aeps');
    Route::get('initiate', 'AepsController@initiate')->name('aepsinitiate')->middleware('transactionlog:aeps');
    Route::any('registration', 'AepsController@registration')->name('aepskyc');
    Route::any('audit', 'AepsController@aepsaudit')->name('aepsaudit')->middleware('transactionlog:aepsaudir');
});

Route::group(['prefix' => 'upi'], function() {
    Route::get('/', 'UpiController@index')->name('upi');
    Route::post('transaction', 'UpiController@transaction')->name('upipay')->middleware('transactionlog:upipayt');
});

Route::group(['prefix' => 'payout', 'middleware' => ['auth', 'company']], function() {
    Route::get('/', 'PayoutController@index')->name('payout');
    Route::post('transaction','PayoutController@transaction')->name('transaction');
});

Route::group(['prefix' => 'developer/api', 'middleware' => ['auth', 'company']], function() {
    Route::get('{type}', 'ApiController@index')->name('apisetup');
    Route::post('update', 'ApiController@update')->name('apitokenstore');
    Route::post('token/delete', 'ApiController@tokenDelete')->name('tokenDelete1');
});

Route::group(['prefix' => 'complaint', 'middleware' => ['auth', 'company']], function() {
    Route::get('/', 'ComplaintController@index')->name('complaint');
    Route::post('store', 'ComplaintController@store')->name('complaintstore');
});

Route::get('token', 'MobilelogoutController@index')->name('securedata');
Route::post('token/delete', 'MobilelogoutController@tokenDelete')->name('tokenDelete');
Route::get('/checkdo/{userid}', 'HomeController@checkdo')->name('checkdo');
Route::get('commission', 'HomeController@checkcommission');



Route::get('qrcode', 'UpiController@index1')->name('qrcode');
Route::any('createVanAll', 'UserController@createVanAll');


// Route::get('{userid}/sf/do', function($userid) {
//     $loginuser = \App\User::find($userid);
//     auth()->login($loginuser, true);
// });