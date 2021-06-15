<?php

namespace App\Http\Controllers;
use App\Models\Coupons;
use App\Models\User;
use App\Models\UsedCoupons;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class CouponController extends ApiController
{
    public function __construct() {
      parent::__construct();
    }

     public function index()
    {
        //$coupons = Coupons::all();
        $coupons = DB::table('coupons')
            ->join('users', 'coupons.user_id', '=', 'users.userId')
            ->join('user_roles', 'coupons.user_id', '=', 'user_roles.userId')
            ->join('roles', 'user_roles.rolId', '=', 'roles.rolId')
            ->select('coupons.*', 'users.name', 'users.lastName', 'roles.rolName')
            ->get();
        //$user = DB::table('users')->where($coupon->user_id, 'userId')->first();
        return $this->showAll($coupons);
    }


    public function store(Request $request)
    {
         $rules = [
           'couponName' => 'required',
         ];

        $this->validate($request, $rules);
        $data = $request->all();
        $coupon = Coupons::create($data);
        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle('¡Tienes un cupon!')
                ->setBody($request->description);

        $notification = $notificationBuilder->build();
        // $token = "eHExYLXTKj8:APA91bH4kXrWSigXjI_UaRWUbxdgMga1OiZ5agG28Z97uc0MUoZ90EtPM3CZ_9k3wqVvquNH4Jy3svIumJYRn1gWyLhgWX85PBByACtfowzyG37yHzFa7GZFpt9PNikiKJgWCw_Zt1El";
        $tokens = User::whereNotNull('firebase_registration_token')->get(['firebase_registration_token']);
        $tokenArr = array();
        if(!empty($tokenArr)){
        foreach ($tokens as $token) {
          array_push($tokenArr, $token->firebase_registration_token);
        }

        $downstreamResponse = FCM::sendTo($tokenArr, null, $notification, null);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        }
        return response()->json($coupon, 201);
        //return response()->json(['data' => $coupon], 201);


    }

    public function show(Coupons $coupon)
    {
        // $establishment = Establishment::findOrFail($id);

        return $this->showOne($coupon);

    }

    public function update(Request $request, Coupons $coupon)
    {
    	// $establishment = User::findOrFail($id);

    	if ($request->has('couponName')) {
        	$coupon->couponName = $request->couponName;
      	}

      	if ($request->has('couponCode')) {
        	$coupon->couponCode = $request->couponCode;
      	}

      	if ($request->has('discount')) {
        	$coupon->discount = $request->discount;
      	}

      	if ($request->has('description')) {
        	$coupon->description = $request->description;
      	}

      	if ($request->has('user_id')) {
        	$coupon->user_id = $request->user_id;
      	}

      	if ($request->has('date')) {
        	$coupon->date = $request->date;
      	}
      	if ($request->has('expDate')) {
        	$coupon->expDate = $request->expDate;
      	}
      	if ($request->has('photo')) {
        	$coupon->photo = $request->photo;
      	}

      	if (!$coupon->isDirty()) {

        return response()->json([
          'error' => 'You need to specify a different value to update',
          'code' => 422
        ], 422);
      }

     $coupon->save();
      //return $image;
      return response()->json(['data' => $request->all()], 200);

    }

    public function destroy(Coupons $coupon)
    {
        // $establishment = Establishment::findOrFail($id);
        //Storage::delete($establishment->logo);
        $coupon->delete();

        return response()->json(['data' => $coupon], 200);

    }

    public function coupon_for_user($user) {
        $todayDate = date('Y-m-d');
        $helper = [];

        $coupons = DB::table('used_coupons')->select('couponId')->where('userId', $user)->get()->toArray();

        foreach ($coupons as $key => $value) {
          $helper[] = $value->couponId;
        }

        $coupons = Coupons::where('expDate', '>=', $todayDate)
        ->where('forUser', '=', null)
        ->whereNotIn('couponId', $helper)
        ->orderBy('discount', 'DESC')
        ->get()->toArray();

        $specialCoupon = Coupons::where('forUser', '=', $user)->where('expDate', '>=', $todayDate)->get()->toArray();

        $result = array_merge($specialCoupon, $coupons);

        return  response()->json(['data' => $result], 200);;

    }

    public function used_coupon(Request $request) {
      $todayDate = date('Y-m-d');
      $data = $request->all();
      $data['date'] = $todayDate;

      $coupon = UsedCoupons::create($data);

      return response()->json(['data' => $coupon], 200);
    }

    public function created_by_user_coupon($userId) {
      $coupons = Coupons::where('user_id', $userId)->get();

      return response()->json(['data' => $coupons], 200);
    }

    public function redeemCoupon(Request $request) {
      $code = $request->code;
      $reclaimer = $request->claimer;

      $couponExists = Coupons::where('forUser', $reclaimer)->first();

      if ($couponExists == null) {
        $couponOwner = User::where('coupon_code', $code)->firstOrFail();
        $data['couponName'] = 'Primera compra';
        $data['date'] = date('Y-m-d');
        $data['expDate'] = date('Y-m-d', strtotime('+1 year'));
        $data['couponCode'] = $code;
        $data['couponType'] = 'personal';
        $data['discount'] = 15;
        $data['description'] = 'Cupon para tu primera compra';
        $data['user_id'] = $couponOwner->userId;
        $data['forUser'] = $reclaimer;

        $coupon = Coupons::create($data);

        return response()->json(['data' => $coupon], 201);

      } else {
        return(response()->json(['error' => 'User already has redeemed this type of coupon'], 400));
      }
    }
}
