<?php

namespace App\Providers;
use App\Models\User;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //URL::forceSchema('https');
        /*Commented out, don't know how to proceed */ 
        // User::created(function($user) {
        //   retry(5, function() use ($user){
        //     Mail::to($user->email)->send(new UserCreated($user));
        //   }, 100);
        // });
        //
        // User::updated(function($user) {
        //   if ($user->isDirty('email')) {
        //     retry(5, function() use ($user){
        //       Mail::to($user->email)->send(new UserMailChanged($user));
        //     }, 100);
        //   }
        // });
    }
}
