<?php

namespace App\Auth\Grants;

use App\Models\User as ModelUser;

use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class SocialGrant extends AbstractGrant
{
    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return UserEntityInterface
     * @throws OAuthServerException
     */
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $token = $this->getRequestParameter('token', $request);
        if (is_null($token)) {
            throw OAuthServerException::invalidRequest('token');
        }

        $network = $this->getRequestParameter('network', $request);
        if (is_null($network)) {
            throw OAuthServerException::invalidRequest('network');
        }

        $email = $this->getRequestParameter('email', $request);
        if (is_null($email)) {
            throw OAuthServerException::invalidRequest('email');
        }

        $user = $this->getUserEntityByUserToken(
            $token,
            $network,
            $email,
            $this->getIdentifier($network),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    /**
     *  Retrieve a user by the given Facebook Id.
     *
     * @param string  $facebookId
     * @param string  $email
     * @param string  $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface  $clientEntity
     *
     * @return \Laravel\Passport\Bridge\User|null
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    private function getUserEntityByUserToken($identifier, $network, $email, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if ($network == 'facebook') {
          $user = (new $model)->where('facebookToken', $identifier)->first();

        } elseif ($network == 'google') {
          $user = (new $model)->where('googleToken', $identifier)->first();
        }


        if (!is_null($user)) {
            // $user = (new $model)->where('email', $email)->first();

            if (is_null($email)) {
                return;
            }

            // Now that we retrieved the user with the code, we need to update it with
            // the proper verification. So the user account will be linked correctly.
            $user->verified = ModelUser::VERIFIED_USER;
            $user->verification_token = null;
            $user->save();
        }

        return new User($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'social';
    }
}
