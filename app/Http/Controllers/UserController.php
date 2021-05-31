<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\invitations;
use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Notifications\SMSCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Image;
use App\Traits\UploadTrait;
//use App\Mail\TestAmazonSes;
//use Mail;

class UserController extends ApiController
{

    use UploadTrait;

    public function __construct() {

      $this->middleware('client.credentials')->except(['store', 'update', 'verify', 'resend', 'requestcode', 'requestcodeNewUser' ,'socialLogin', 'getuserbyemail', 'checkToken', 'verifyUserExistsByCellphone', 'getuserbycellphone']);
      $this->middleware('auth:api')->except(['store', 'update', 'verify', 'resend', 'requestcode', 'requestcodeNewUser' ,'socialLogin', 'checkToken','getuserbyemail', 'verifyUserExistsByCellphone', 'getuserbycellphone']);
      $this->middleware('scope:super-admin')->only(['index']);
      $this->middleware('can:view,user')->only('show');
      //$this->middleware('can:update,user')->only('update');
      $this->middleware('can:delete,user')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
      $this->allowedAdminAction();

      $user = User::all();

      return $this->showAll($user);
    }

    public function store(Request $request) {

      if($request->network == "facebook" || $request->network == "google") {
        $rules = [
          'email' => 'required|email|unique:users'
        ];
      } else if($request->network == "cellphone") {
        $rules = [
          'email' => 'required|email|unique:users',
          'cellphone' => 'required|min:11|unique:users'
        ];
      } else {
        $rules = [
          'email' => 'required|email|unique:users',
          'password' => 'required|min:8|confirmed'
        ];
      }

      $this->validate($request, $rules);

      $data = $request->all();
      $data['password'] = bcrypt($request->password);
      $data['verified'] = User::UNVERIFIED_USER;
      $data['verification_token'] = User::generateVerificationCodeCellphone();
      $data['status'] = 'Inactivo';
      $data['isLogged'] = false;
      $data['coupon_code'] = User::generatePersonalCouponCode();

      if (!empty($data['photo']) && $request->network != "facebook" && $request->network != "google") {
        $data['photo'] = $request->photo->store('');
      }

      $user = User::create($data);

      if($request->has('rolID')) {
        $rolId = $request->rolId;
      } else {
        $rolId = 5;
      }

      DB::table('user_roles')->insert([
        'userId' => $user->userId,
        'rolId' => $rolId
      ]);

      $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
      $user->notify(new SMSCode($user->verification_token));

      return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
      public function show(User $user) {
        // $user = User::findOrFail($id);

        return $this->showOne( $user);

      }

    public function getUserByEmail($email){
      $user = DB::table('users')
      ->where('googleToken', $email)->first();

      //$user = DB::select('SELECT * FROM users WHERE email = ?' , $email);
      return response()->json(['data' => $user], 201);
      //return "hola";
    }

    public function getUserByCellphone($cellphone){
      $user = DB::table('users')->where('cellphone', $cellphone)->first();

      return response()->json(['data' => $user], 201);
      //return $cellphone;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {

      $request->validate([
        'email' => 'email|unique:users,email,'.$user->userId.',' . $user->getKeyName(),
        'password' => 'min:8|confirmed'
      ]);

      if ($request->has('name')) {
        $user->name = $request->name;
      }

      if ($request->has('lastName')) {
        $user->lastName = $request->lastName;
      }

      if ($request->has('email') && $user->email != $request->email) {
        $user->verified = User::UNVERIFIED_USER;
        $user->verification_token = User::generateVerificationCode();
        $user->email = $request->email;
      }

      if ($request->has('password')) {
        $user->password = bcrypt($request->password);
      }

      if ($request->has('open_pay_token')) {
        $user->open_pay_token = $request->open_pay_token;
      }

      if ($request->has('paypal_token')) {
        $user->paypal_token = $request->paypal_token;
      }

      if ($request->has('current_gate')) {
        $user->current_gate = $request->current_gate;
      }

      if ($request->has('image')) {
        if ($user->photo == null || $user->photo == "" || $user->photo== "undefined") {
          $user->photo = $request->image;
        }
      }

      if ($request->has('facebookToken')) {
        $user->facebookToken = $request->facebookToken;
      }

      if ($request->has('googleToken')) {
        $user->googleToken = $request->googleToken;
      }

      if ($request->hasFile('photo')) {
        Storage::delete($user->photo);
        $image = $request->file('photo');
        $folder = 'uploads/images/';
        $name =  str_random(25);
        $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        $image->move($folder, $name. '.' . $image->getClientOriginalExtension());
        //$this->uploadOne($image, $folder, 'public', $name);
        $user['photo'] = $filePath;
        }
      // if ($request->hasFile('photo')) {
      //   Storage::delete($user->photo);

      //   $user->photo = $request->photo->store('');
      // }

      if (!$user->isDirty()) {
        return $this->errorResponse('You need to specify a different value to update', 422);
      }

      $user->save();

      return $this->showOne($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        Storage::delete($user->photo);
        $user->delete();

        return $this->showOne($user);

    }

    public function verify($token) {
      $user = User::where('verification_token', $token)->firstOrFail();

      $user->verified = User::VERIFIED_USER;
      $user->verification_token = null;

      $user->save();

      return $this->showMessage('The account has been verified succesfully');
    }

    public function resend(User $user) {
      if ($user->isVerified()) {
        return $this->errorResponse('This user is already verified', 409);
      }

      retry(5, function() use ($user){
        Mail::to($user->email)->send(new UserCreated($user));
      }, 100);

      return $this->showMessage('The verification email has been resend');
    }

    public function requestcode($cellphone) {
      $user = User::firstOrNew(['cellphone'=> $cellphone]);

      $user->verification_token = User::generateVerificationCodeCellphone();
      $user->status = 'Inactivo';
      $user->isLogged = false;
      $user->verified = User::UNVERIFIED_USER;
      if($user->coupon_code == null || $user->coupon_code == ""){
        $user->coupon_code = User::generatePersonalCouponCode();
      }

      $user->save();

      $roles = DB::table('user_roles')->where('userId', $user->userId)->get();
      if(count($roles)<1){
        DB::table('user_roles')->insert([
          'userId' => $user->userId,
          'rolId' => 5
        ]);
        $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
       $user->notify(new SMSCode($user->verification_token));

          return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
      } else {
        foreach ($roles as $role) {
          if ($role->userId == $user->userId && $role->rolId == 5) {
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
           $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          } else {
            DB::table('user_roles')->insert([
              'userId' => $user->userId,
              'rolId' => 5
            ]);
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
           $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          }
        }
      }

    }

    public function requestcodeNewUser(Request $request) {
      $data = $request->all();

      $user = User::firstOrNew(['cellphone'=> $data['cellphone']]);

      $user->verification_token = User::generateVerificationCodeCellphone();
      $user->status = 'Inactivo';
      $user->isLogged = false;
      $user->verified = User::UNVERIFIED_USER;
      if($user->coupon_code == null || $user->coupon_code == ""){
        $user->coupon_code = User::generatePersonalCouponCode();
      }

      $user->name = $data['name'];
      $user->lastName = $data['lastName'];
      $user->email = $data['email'];
      $user->open_pay_token = null;
      $user->save();

      $roles = DB::table('user_roles')->where('userId', $user->userId)->get();
      if(count($roles)<1){
        DB::table('user_roles')->insert([
          'userId' => $user->userId,
          'rolId' => 5
        ]);
        $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
        $user->notify(new SMSCode($user->verification_token));

          return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
      } else {
        foreach ($roles as $role) {
          if ($role->userId == $user->userId && $role->rolId == 5) {
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
            $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          } else {
            DB::table('user_roles')->insert([
              'userId' => $user->userId,
              'rolId' => 5
            ]);
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
           $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          }
        }
      }

    }

    public function logout() {
      $user = Auth::user()->token();
      $user->revoke();
      return $this->showMessage('Successfully logged out');
    }

    public function socialLogin(Request $request, User $user){
      $data = $request->all();

      $user = User::firstOrNew(['cellphone'=> $data['cellphone']]);

      if ($user->name == null || $user->name == "") {
        $user->name = $data['name'];
      }
      if ($user->lastName == null || $user->lastName == "") {
        $user->lastName = $data['lastName'];
      }
      if ($user->email == null || $user->email == "") {
        $user->email = $data['email'];
      }
      if ($user->photo == null || $user->photo == "") {
        $user->photo = $data['image'];
      }
      if ($user->cellphone == null || $user->cellphone == "") {
        $user->cellphone = $data['cellphone'];
      }
      if ($user->open_pay_token == null || $user->open_pay_token == "") {
        $user->open_pay_token = $data['open_pay_token'];
      }
      if($user->coupon_code == null || $user->coupon_code == ""){
        $user->coupon_code = User::generatePersonalCouponCode();
      }

      $user->verification_token = User::generateVerificationCodeCellphone();
      $user->status = 'Inactivo';
      $user->isLogged = false;
      $user->verified = User::UNVERIFIED_USER;

      if ($data['network'] == 'facebook') {
        $user->facebookToken = $data['token'];
      }

      if ($data['network'] == 'google') {
        $user->googleToken =  $data['token'];
      }

      $user->save();

      $roles = DB::table('user_roles')->where('userId', $user->userId)->get();


      if(count($roles)<1){
        DB::table('user_roles')->insert([
          'userId' => $user->userId,
          'rolId' => 5
        ]);
        $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
        $user->notify(new SMSCode($user->verification_token));

          return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
      } else {
        foreach ($roles as $role) {
          if ($role->userId == $user->userId && $role->rolId == 5) {
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
           $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          } else {
            DB::table('user_roles')->insert([
              'userId' => $user->userId,
              'rolId' => 5
            ]);
            $user->phone_number= '+'.$user->cellphone;   // Don't forget specify country code.
           $user->notify(new SMSCode($user->verification_token));

            return response()->json(['code'=> $user->verification_token, 'userId' => $user->userId], 200);
          }
        }
      }

    }

    public function ownerRegister(Request $request, USER $user) {
      $data = $request->all();
      $user = User::where('cellphone', $data['cellphone'])->firstOrFail();

      if ($user->name == "" || $user->name == null)
        $user->name = $data['firstName'];
      if ($user->lastName == "" || $user->lastName == null)
        $user->lastName = $data['lastName'];
      if ($user->photo == "" || $user->photo == null)
        $user->photo = $data['photo'];
      if ($user->email == "" || $user->email == null)
        $user->email = $data['email'];

      $user->status = 'Activo';
      $user->isLogged = true;
      $user->verified = User::VERIFIED_USER;

      if($user->coupon_code == null || $user->coupon_code == ""){
        $user->coupon_code = User::generatePersonalCouponCode();
      }
      $user->save();

      DB::table('establishments')->insert([
        'ownerId' => $user->userId,
        'name' => $data['name'],
        'location' => $data['location'],
        'address' =>  $data['address'],
        'capacity' => $data['capacity'],
        'logo' => $data['logo'],
      ]);

      DB::table('user_roles')->insert([
          'userId' => $user->userId,
          'rolId' => '2'
        ]);
      $user->phone_number= '+'.$data['cellphone'];   // Don't forget specify country code.
      $user->notify(new SMSCode($user->verification_token));

      return response()->json([$request->all()], 200);
     }

    public function checkToken($token, $network){
      if( $network == 'facebook'){
        $user = User::where('facebookToken', $token)->firstOrFail();
      } else if( $network == 'google'){
        $user = User::where('googleToken', $token)->firstOrFail();
      }

      $user->status = 'Activo';
      $user->isLogged = true;
      $user->verified = User::VERIFIED_USER;
      if($user->coupon_code == null || $user->coupon_code == ""){
        $user->coupon_code = User::generatePersonalCouponCode();
      }

      $user->save();

      return response()->json(['userId' => $user->userId], 200);

    }

    public function get_user_establishments($userId){
      $establishments = Establishment::where('ownerId', $userId)->get();

      return response()->json(['data' => $establishments], 200);

    }

    public function saveFireBaseToken(Request $request){
      $token = $request->token;
      $userId = $request->userId;

      $user = User::where('userId', $userId)->firstOrFail();

      $user->firebase_registration_token = $token;

      $user->save();

      return response()->json(['userId' => $user->userId], 200);

    }

    public function getAllFirebaseTokens() {
      $tokens = User::whereNotNull('firebase_registration_token')->get(['firebase_registration_token']);

      return $tokens;
    }

    public function verifyUserExistsByCellphone($number){
      $user = User::where('cellphone', $number)->firstOrFail();

      return response()->json(['userId' => $user->userId], 200);
    }

    public function getAllUsers($id){



      $user = DB::table('users')->get();
       //echo($user);
       return $this->showAll($user);
      //return response()->json(['userId' => $user->userId], 200);

    }



    public function agregarInvitacion($info){

      $data = array('name'=>"jesus","body"=>"Testsmail");

      Mail::send('tpl', $data, function($message)
       {
      $message->from('fetchup@fetchup.io', "Admin - ****");
      $message->subject("Alta de administrador");
      $message->to('toktokdevelopment@gmail.com');
       });

      echo($request);
      /*DB::table('invitations')->insert([
        'accountId' => $user->userId,
        'sernderId' => $rolId,
        'email' => $rolId,
        'message' => $rolId,
        'rolId' => $rolId
      ]);*/

    }

}
