<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

namespace J5\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Trackmania extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'login';

    public function __construct(array $options = [], array $collaborators = [])
    {
        $options['verify'] = $options['verify'] ?? false;
        parent::__construct($options, $collaborators);
    }

    protected function getAllowedClientOptions(array $options)
    {
        $client_options = parent::getAllowedClientOptions($options);

        $client_options[] = 'verify';

        return $client_options;
    }

    public function getBaseAuthorizationUrl()
    {
        return 'https://ws.trackmania.com/oauth2/authorize/';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://ws.trackmania.com/oauth2/token/';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://ws.trackmania.com/player/';
    }

    protected function getDefaultScopes()
    {
        return ['basic'];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            throw new IdentityProviderException($data['error'], 0, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new TrackmaniaPlayer($response);
    }
}
