<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Auth\Grants\CellphoneGrant;
use App\Auth\Grants\SocialGrant;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * d1JYiiaBkLDmh2dWm2g1SQjoMN7t9d7SpAw0slcj
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        app(AuthorizationServer::class)->enableGrantType(
          $this->makeCellphoneGrant(), Passport::tokensExpireIn()
        );

        app(AuthorizationServer::class)->enableGrantType(
          $this->makeSocialGrant(), Passport::tokensExpireIn()
        );

        Gate::define('admin-action', function($user) {
          return $user->isAdmin();
        });

        Passport::routes(null, ['prefix' => 'api/oauth']);
        Passport::tokensExpireIn(Carbon::now()->addDays(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();
        Passport::tokensCan([
          'super-admin' => 'The user can do anything in the backend',
          'admin-establishment' => 'The user is the owner of the establishment and can update its own establishment',
          'admin-marketing' => 'The user can upload ads to a designated establishment',
          'operator' => 'The user can close transactions and read some user data of users in the establishment',
          'user' => 'Basic user of the app'
        ]);
    }

    /**
      * Create and configure a Cellphone grant instance.
      *
      * @return CellphoneGrant
      */
      protected function makeCellphoneGrant() {
        $grant = new CellphoneGrant(
          $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
      }

      /**
        * Create and configure a Social grant instance.
        *
        * @return SocialGrant
        */
        protected function makeSocialGrant() {
          $grant = new SocialGrant(
            $this->app->make(RefreshTokenRepository::class)
          );

          $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

          return $grant;
        }
}
