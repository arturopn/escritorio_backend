<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    protected $primaryKey = 'userId';

    // public function routeNotificationForNexmo() {
    //   return $this->cellphone;
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lastName',
        'email',
        'password',
        'cellphone',
        // 'dateCreated',
        // 'dateUpdated',
        'facebookToken',
        'googleToken',
        'photo',
        'gender',
        'age',
        'status',
        'isLogged',
        'verified',
        'verification_token',
        'coupon_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'facebookToken',
        'verification_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVerified() {
      return $this->verified == User::VERIFIED_USER;
    }

    public function isAdmin() {

     $admin = DB::table('user_roles')->where([
        ['userId', '=', $this->userId],
        ['rolId', '=', '1'],
      ])->first();

      if (!empty($admin)) {
        return true;
      } else {
        return false;
      }

    }

    public function isOwner() {

     $owner = DB::table('user_roles')->where([
        ['userId', '=', $this->userId],
        ['rolId', '=', '2'],
      ])->first();

      if (!empty($owner)) {
        return true;
      } else {
        return false;
      }

    }

    public static function generateVerificationCode() {
      return str_random(40);
    }

    public static function generateVerificationCodeCellphone() {
      return random_int(1000, 9999);
    }

    public static function generatePersonalCouponCode() {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randomString = '';

      for ($i = 0; $i < 10; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
      }
      return $randomString;
    }

}
