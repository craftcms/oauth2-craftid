<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Craft extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';

    /**
     * @var array Additional fields to be requested from the user profile.
     *            If set, these values will be included with the defaults.
     */
    protected $userFields = [];

    public function getBaseAuthorizationUrl()
    {
        return 'http://craftcms.dev/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'http://craftcms.dev/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'http://craftcms.dev/api/account';
    }

    protected function getDefaultScopes()
    {
        return [
            'account',
        ];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $code  = 0;
            $error = $data['error'];

            if (is_array($error)) {
                $code  = $error['code'];
                $error = $error['message'];
            }

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new CraftUser($response);
    }
}
