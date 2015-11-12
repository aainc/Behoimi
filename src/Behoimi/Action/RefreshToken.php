<?php
namespace Behoimi\Action;

use Behoimi\OAuth\AccessToken;
use Behoimi\OAuth\InvalidRefreshTokenException;
use Behoimi\Response\EntityResult;
use Hoimi\Exception\ValidationException;

class RefreshToken extends ApiBaseAction
{
    public function get()
    {
        $request = $this->getRequest();
        $validationResult = \Hoimi\Validator::validate($request, array(
            'refresh_token' => array('required' => true),
        ));

        if ($validationResult) {
            throw new ValidationException($validationResult);
        }

        $accessToken = (object) array(
            'access_token' => bin2hex(openssl_random_pseudo_bytes(25)),
            'refresh_token' => $request->get('refresh_token'),
            'expired_in' => AccessToken::EXPIRED_IN,
        );

        $result = $this->getDatabaseSession()->executeNoResult(
            'UPDATE access_tokens SET access_token = ?, created_at = UNIX_TIMESTAMP() WHERE refresh_token = ?',
            'ss',
            array(
                $accessToken->access_token,
                $accessToken->refresh_token,
            ));
        if ($result < 1) {
            throw new InvalidRefreshTokenException();
        }

        return new EntityResult(true, $accessToken, null);
    }
}
