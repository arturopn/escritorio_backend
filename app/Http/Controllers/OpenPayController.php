<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Openpay;
use Exception;
use OpenpayApiError;
use OpenpayApiAuthError;
use OpenpayApiRequestError;
use OpenpayApiConnectionError;
use OpenpayApiTransactionError;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

require_once '../vendor/autoload.php';


class OpenPayController extends ApiController
{
        /**
     * Create charge in OpenPay
     * https://www.openpay.mx/docs/api/?php#con-id-de-tarjeta-o-token
     *
     */

     public function __construct() {

      $this->middleware('client.credentials')->except(['addCustomer', 'addCard','getCard','deleteCard','getBankAccount','addBankAccount','getListofBankAccounts']);
      $this->middleware('auth:api')->except(['addCustomer', 'addCard','getCard','deleteCard','getBankAccount','addBankAccount','getListofBankAccounts']);
      $this->middleware('scope:super-admin')->only(['index']);
      $this->middleware('can:view,user')->only('show');
      $this->middleware('can:update,user')->only('update');
      $this->middleware('can:deleteCustomer,user')->only('destroy');
    }

    public function index(){
        $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
        Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));
        $openpay->charges->list('aycypmmyjeuebxvssqck');
      // try {
      // // create instance OpenPay
      //   $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));

      //   Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

      //   // create object customer
      //   $customer = array(
      //     'name' => $request->name,
      //     'last_name' => $request->last_name,
      //     'email' => $request->email
      //   );

      //   // create object charge
      //   $chargeRequest =  array(
      //     'method' => 'card',
      //     'source_id' => $request->token,
      //     'amount' => $request->amount,
      //     'currency' => $request->currency,
      //     'description' => $request->description,
      //     'device_session_id' => $request->deviceSessionId,
      //     'customer' => $customer
      //   );

      //   $charge = $openpay->charges->create($chargeRequest);

      //   return response()->json([
      //     'data' => $charge->id
      //   ]);

      // } catch (OpenpayApiTransactionError $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // } catch (OpenpayApiRequestError $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // } catch (OpenpayApiConnectionError $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // } catch (OpenpayApiAuthError $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // } catch (OpenpayApiError $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // } catch (Exception $e) {
      //   return response()->json([
      //     'error' => [
      //       'category' => $e->getCategory(),
      //       'error_code' => $e->getErrorCode(),
      //       'description' => $e->getMessage(),
      //       'http_code' => $e->getHttpCode(),
      //       'request_id' => $e->getRequestId()
      //     ]
      //   ]);
      // }
    }

    public function addCustomer(Request $request){
        try {
        if(getenv('OPENPAY_ID')) {
          $OPENPAY_ID = getenv('OPENPAY_ID');
          $OPENPAY_SK = getenv('OPENPAY_SK');
        }
        else{
          $OPENPAY_ID = env('OPENPAY_ID');
          $OPENPAY_SK = env('OPENPAY_SK');
        }

            $openpay = Openpay::getInstance($OPENPAY_ID, $OPENPAY_SK);
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));
            $customerData = array(
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->cellphone,
            'address' => array(
                    'line1' => $request->line1,
                    'line2' => $request->line2,
                    'line3' => $request->line3,
                    'postal_code' => $request->postal_code,
                    'state' => $request->state,
                    'city' => $request->city,
                    'country_code' => $request->country_code));
             $customer = $openpay->customers->add($customerData);
             return response()->json([
                    'data' => 'Se creo la cuenta del establecimiento',
                    'token' => $customer->id
                ]);
       }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }
    }

    public function addEstablishment(Request $request){

        try {

            if($openpayid = getenv('OPENPAY_ID')){
              $openpay = Openpay::getInstance(getenv('OPENPAY_ID'), getenv('OPENPAY_SK'));
              Openpay::setProductionMode(getenv('OPENPAY_PRODUCTION_MODE'));
            }
            else{
              $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
              Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));
            }

            $holder_name = $request->first_name." ".$request->last_name;
            $customerData = array(
            'name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->cellphone,
            'address' => array(
                    'line1' => $request->line1,
                    'line2' => $request->line2,
                    'line3' => $request->line3,
                    'postal_code' => $request->postal_code,
                    'state' => $request->state,
                    'city' => $request->city,
                    'country_code' => $request->country_code)
            );

            $customer = $openpay->customers->add($customerData);
            $id = $customer->id;
            if($customer){
              $establishment = DB::table('establishments')
              ->join('users', 'establishments.ownerId', '=', 'users.userId')
              ->where('users.email', $request->email)
              ->update(['establishments.openpayid' => $customer->id]);
            }
            // $bankData = array(
            // 'clabe' => $request->clabe,
            // 'alias' => 'cuenta clabe',
            // 'holder_name' => holder_name);

            // $bankaccount = $customer->bankaccounts->add($bankData);


             return response()->json([
                    'data' => 'Se creo la cuenta del establecimiento',
                    'token' => $customer->id
                ]);
       }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function getCustomer(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
            return response()->json([
                    'data' => $customer
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function getListofCustomers(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $findData = array(
            'creation[gte]' => '2013-01-01',
            'creation[lte]' => '2013-12-31',
            'offset' => 0,
            'limit' => 5);

             $customerList = $openpay->customers->getList($findData);
            return response()->json([
                    'data' => $customer
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }

    }

    public function updateCustomer(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
            $customer->name = $request->name;
            $customer->last_name = $request->last_name;
            $customer->save();
            return response()->json([
                    'data' => $customer
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function deleteCustomer(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $customer = $openpay->customers->get($request->customerId);
            $customer->delete();
            return response()->json([
                    'data' => $customer
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function addCard(Request $request){

      try {
        $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
        Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

        $cardData = array(
            'holder_name' => $request->name,
            'card_number' => $request->card_number,
            'cvv2' => $request->cvv,
            'expiration_month' => $request->expiration_month,
            'expiration_year' => $request->expiration_year,
            'address' => array(
                    'line1' => $request->line1,
                    'line2' => $request->line2,
                    'line3' => $request->line3,
                    'postal_code' => $request->postal_code,
                    'state' => $request->state,
                    'city' => $request->city,
                    'country_code' => $request->currency));

        $customer = $openpay->customers->get('acwqkoksb7vtv2vm5mzc');
        $card = $customer->cards->add($cardData);

         return response()->json([
                'data' => 'Se agrego la tarjeta'
            ]);

      }  catch (OpenpayApiTransactionError $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      } catch (OpenpayApiRequestError $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      } catch (OpenpayApiConnectionError $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      } catch (OpenpayApiAuthError $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      } catch (OpenpayApiError $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      } catch (Exception $e) {
        return response()->json([
            'error' => [
                'category' => $e->getCategory(),
                'error_code' => $e->getErrorCode(),
                'description' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'request_id' => $e->getRequestId()
            ]
        ]);
      }

    }

    public function getCard(Request $request){

      try {
        $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
        Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

        $customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
        $card = $customer->cards->get('k89i8mrlkzbgnslhihbk');
        return response()->json(['data' => $card]);

      }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
      }
    }

    public function getListofCards(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $findData = array(
            'creation[gte]' => '2013-01-01',
            'creation[lte]' => '2013-12-31',
            'offset' => 0,
            'limit' => 5);

            $customer = $openpay->customers->get('acwqkoksb7vtv2vm5mzc');
            $cardList = $customer->cards->getList($findData);
            return response()->json([
                    'data' => $card
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function deleteCard(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $customer = $openpay->customers->get('acwqkoksb7vtv2vm5mzc');
            $card = $customer->cards->get('k89i8mrlkzbgnslhihbk');
            $card->delete();
            return response()->json([
                    'data' => $card
                ]);
        }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }


    }

    public function addBankAccount(Request $request){

        try {
            if($openpayid = getenv('OPENPAY_ID')){
              $openpay = Openpay::getInstance(getenv('OPENPAY_ID'), getenv('OPENPAY_SK'));
              Openpay::setProductionMode(getenv('OPENPAY_PRODUCTION_MODE'));
            }
            else{
              $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
              Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));
            }

            $bankData = array(
            'clabe' => $request->clabe,
            'alias' => 'Cuenta Bancaria',
            'holder_name' => $request->holder_name);

            $customer = $openpay->customers->get($request->id);
            $bankaccount = $customer->bankaccounts->add($bankData);

             return response()->json([
                    'data' => 'Se agrego la cuenta'
                ]);

         }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }

    }

    public function getBankAccount(){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $customer = $openpay->customers->get('acwqkoksb7vtv2vm5mzc');
            $bankaccount = $customer->bankaccounts->get('bugqem8kvqxzzuvombez');

             return response()->json([
                    'data' => $bankaccount
                ]);

         }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }

    }

    public function getListofBankAccount(Request $request){

        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

            $findData = array(
            'creation[gte]' => '2013-01-01',
            'creation[lte]' => '2013-12-31',
            'offset' => 0,
            'limit' => 5);

           $customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
           $bankaccountList = $customer->bankaccounts->getList($findData);

             return response()->json([
                    'data' => $bankaccountList
                ]);

         }  catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }

    }

    public function performFeeCharge(Request $request) {
      try {
        // create instance OpenPay
        $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));

        Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

        $customer = $openpay->customers->get($request->payer);

        $transferDataRequest = array(
          'customer_id' => $request->receiver,
          'amount' => $request->total,
          'description' => 'Payment for parking TokTok',
        );

        $transfer = $customer->transfers->create($transferDataRequest);

        // create object charge
        $feeDataRequest =  array(
          'customer_id' => $request->receiver,
          'amount' => $request->fee,
          'description' => 'Fee payment to TokTok',
        );

        $charge = $openpay->fees->create($feeDataRequest);

        return response()->json([
          'data' => $charge
        ]);
        // $receiver = $openpay->customers->get($request->receiver);
        //
        // $findDataRequest = array(
        //   'creation[gte]' => '2020-01-01',
        // );
        //
        // $bankaccountList = $receiver->bankaccounts->getList($findDataRequest);
        //
        // $bankAccountToPay = $bankaccountList[0]->id;
        //
        // $payoutRequest = array(
        //   'method' => 'bank_account',
        //   'destination_id' => $bankAccountToPay,
        //   'amount' => $request->total,
        //   'description' => 'Payment from TokTok',
        // );
        //
        // $payout = $receiver->payouts->create($payoutRequest);
        //
        // return response()->json([
        //   'data' => $payout
        // ]);

      } catch (OpenpayApiTransactionError $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      } catch (OpenpayApiRequestError $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      } catch (OpenpayApiConnectionError $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      } catch (OpenpayApiAuthError $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      } catch (OpenpayApiError $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      } catch (Exception $e) {
        return response()->json([
          'error' => [
            'category' => $e->getCategory(),
            'error_code' => $e->getErrorCode(),
            'description' => $e->getMessage(),
            'http_code' => $e->getHttpCode(),
            'request_id' => $e->getRequestId()
          ]
        ]);
      }
    }
}
