<?php

namespace App\Http\Controllers\Api\Auth;

use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Zend\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;

class OAuthController extends \Laravel\Passport\Http\Controllers\AccessTokenController
{
    public $allowMultipleTokens = false;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct($server, $tokens, $jwt);
    }

    public function issueAccessToken(ServerRequestInterface $request)
    {
        $response = $this->withErrorHandling(function () use ($request) {
            $input = (array) $request->getParsedBody();
            $clientId = isset($input['client_id']) ? $input['client_id'] : null;

            $grant = 'makePasswordGrant';
            switch ($input['grant_type']) {
                case 'refresh_token':
                     $grant = 'makeRefreshTokenGrant';
                    break;
            }

            Passport::tokensExpireIn(now()->addSeconds(3600));
            Passport::refreshTokensExpireIn(now()->addDays(30));

            // Overwrite password grant at the last minute to add support for customized TTLs
            $this->server->enableGrantType(
                $this->{$grant}(), Passport::tokensExpireIn(null, $clientId)
            );

            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        });

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return $response;
        }

        $payload = json_decode($response->getBody()->__toString(), true);

        if (isset($payload['access_token'])) {
            $tokenId = $this->jwt->parse($payload['access_token'])->getClaim('jti');
            $token = $this->tokens->find($tokenId);

            if ($token->client->firstParty() && $this->allowMultipleTokens) {
                // We keep previous tokens for password clients
            } else {
                $this->revokeOrDeleteAccessTokens($token, $tokenId);
            }
        }

        return $response;
    }

    private function makePasswordGrant()
    {
        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
            app()->make(\Laravel\Passport\Bridge\UserRepository::class),
            app()->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    private function makeRefreshTokenGrant()
    {
        $repository = app()->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class);

        return tap(new \League\OAuth2\Server\Grant\RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        });
    }

    /**
     * Instruct Passport to keep revoked tokens pruned.
     */
    public function allowMultipleTokens()
    {
        $this->allowMultipleTokens = true;
    }

    public function revokeOrDeleteAccessTokens(Token $token, $tokenId)
    {
        $query = Token::where('user_id', $token->user_id)->where('client_id', $token->client_id);

        if ($tokenId) {
            $query->where('id', '<>', $tokenId);
        }

        if (Passport::$pruneRevokedTokens) {
            $query->delete();
        } else {
            $query->update(['revoked' => true]);
        }
    }
}
