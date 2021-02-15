<?php

use Illuminate\Http\Request;
// use PayPal\Api\Amount;
// use PayPal\Api\Details;
// use PayPal\Api\Item;
// use PayPal\Api\ItemList;
// use PayPal\Api\Payer;
// use PayPal\Api\Payment;
// use PayPal\Api\RedirectUrls;
// use PayPal\Api\Transaction;
// use PayPal\Api\InputFields;
// use PayPal\Api\WebProfile;

  //header('Access-Control-Allow-Origin:  *');
  //header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE, OPTIONS');
  //header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * Users
 *
 */
Route::apiResource('users', 'UserController');
Route::name('getuserbyemail')->get('users/getUserByEmail/{email}', 'UserController@getUserByEmail');
Route::name('getuserbycellphone')->get('users/getUserByCellphone/{cellphone}', 'UserController@getUserByCellphone');
Route::name('verify')->get('users/verify/{token}', 'UserController@verify');
Route::name('resend')->get('users/{user}/resend', 'UserController@resend');
Route::name('requestcode')->get('users/{cellphone}/requestcode', 'UserController@requestcode');
Route::name('requestcodenewuser')->post('users/requestcode/newUser', 'UserController@requestcodeNewUser');
Route::name('logout')->get('users/auth/logout', 'UserController@logout');
Route::name('socialLogin')->post('users/social/requestCode', 'UserController@socialLogin');
Route::name('ownerRegister')->post('users/establishment/register', 'UserController@ownerRegister');
Route::name('checkToken')->get('users/tokencheck/{token}/{network}', 'UserController@checkToken');
Route::name('getAllFirebaseTokens')->get('users/firebaseToken/all', 'UserController@getAllFireBaseTokens');
Route::name('saveFireBaseToken')->post('users/firebaseToken', 'UserController@saveFireBaseToken');
Route::name('verifyUserExistsByCellphone')->get('users/verifyUserExistsByCellphone/{cellphone}', 'UserController@verifyUserExistsByCellphone');
Route::name('getAllUsers')->get('users/getAllUsers/{id}', 'UserController@getAllUsers');
Route::name('agregarInvitacion')->get('users/agregarInvitacion/{info}', 'UserController@agregarInvitacion');

/**
 * Establishments
 */
Route::apiResource('establishments', 'EstablishmentController');
Route::name('nearPlaces')->post('establishments/nearPlaces', 'EstablishmentController@get_near_places');
Route::name('calculate_payment')->post('establishments/calculatePayment', 'EstablishmentController@calculate_payment');
Route::name('get_user_establishments')->get('establishments/userEstablishments/{userId}', 'EstablishmentController@get_user_establishments');
Route::name('getEstablishmentsInformation')->post('establishments/getInfo', 'EstablishmentController@getEstablishmentsInformation');
Route::name('createBill')->post('establishments/createBill', 'EstablishmentController@createBill');

Route::name('getEstablishmentsUsers')->get('establishments/establishmentsUsers/{nu}','EstablishmentController@getEstablishmentsUsers');


/**
 * Coupons
*/
Route::apiResource('coupons', 'CouponController');
Route::name('coupon_for_user')->get('coupons/getCouponForUser/{userId}', 'CouponController@coupon_for_user');
Route::name('used_coupon')->post('coupons/usedCoupon', 'CouponController@used_coupon');
Route::name('created_by_user_coupon')->get('coupons/createdBy/{userId}', 'CouponController@created_by_user_coupon');
Route::name('redeemCoupon')->post('coupons/redeemCoupon/', 'CouponController@redeemCoupon');

/**
 * OpenPay
*/
Route::apiResource('openpay', 'OpenPayController');
Route::name('addcustomer')->post('openpay/addCustomer', 'OpenPayController@addCustomer');
Route::name('addcustomer')->post('openpay/addEstablishment', 'OpenPayController@addEstablishment');
Route::name('deleteCustomer')->post('openpay/deleteCustomer', 'OpenPayController@deleteCustomer');
Route::name('addcard')->post('openpay/addCard', 'OpenPayController@addCard');
Route::name('getcard')->get('openpay/getCard', 'OpenPayController@getCard');
Route::name('deletecard')->put('openpay/deleteCard', 'OpenPayController@deleteCard');
Route::name('addbankaccount')->post('openpay/addBankAccount', 'OpenPayController@addBankAccount');
Route::name('getbankaccount')->get('openpay/getBankAccount', 'OpenPayController@getBankAccount');
Route::name('updatebankaccount')->get('openpay/updateBankAccount', 'OpenPayController@updateBankAccount');
Route::name('deleteBankAccount')->get('openpay/deleteBankAccount', 'OpenPayController@deleteBankAccount');
Route::name('performFeeCharge')->post('openpay/performFeeCharge', 'OpenPayController@performFeeCharge');

/*
*Billing
*/
/**
 * Rates
*/
Route::apiResource('rate', 'RateController');
Route::name('created_by_user_rate')->get('rate/createdBy/{userId}', 'RateController@created_by_user_rate');


/**
 * QR
*/
Route::apiResource('qr', 'QRController');
Route::name('created_by_user_qr')->get('qr/createdBy/{userId}', 'QRController@created_by_user_qr');


/**
 * OAuth
*/

Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
//URL::forceScheme('https');


Route::resource('services', 'ServicesAPIController');


Route::resource('users_lawyer_chats', 'UsersLawyerChatAPIController');
Route::name('sendnotification')->post('users_lawyer_chats/sendNotification', 'UsersLawyerChatAPIController@sendNotification');