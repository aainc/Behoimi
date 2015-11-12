<?php
namespace Behoimi\Action;

use Behoimi\OAuth\AccessToken;
use Behoimi\OAuth\InvalidCodeException;
use Behoimi\Response\EntityResult;
use Hoimi\Exception\ValidationException;
use Hoimi\Validator;

class ExchangeToken extends ApiBaseAction
{
    public function get()
    {
        $request = $this->getRequest();
        $validationResult = Validator::validate($request, array(
            'code' => array('required' => true, 'dataType' => 'string'),
        ));
        if ($validationResult) {
            throw new ValidationException($validationResult);
        }
        $this->getDatabaseSession()->executeNoResult('DELETE FROM authorization_codes WHERE expired_at < UNIX_TIMESTAMP()');
        $code = $this->getDatabaseSession()->find(
            'SELECT id, authorized_application_id FROM authorization_codes WHERE code = ?',
            's',
            array($request->get('code'))
        );
        if (!$code) {
            throw new InvalidCodeException();
        }
        $accessToken = (object) array(
            'access_token' => bin2hex(openssl_random_pseudo_bytes(25)),
            'refresh_token' => bin2hex(openssl_random_pseudo_bytes(25)),
            'expired_in' => AccessToken::EXPIRED_IN,
        );
        $this->getDatabaseSession()->executeNoResult(
            'INSERT INTO access_tokens (authorized_application_id, access_token, refresh_token, created_at) VALUES (?, ?, ?, UNIX_TIMESTAMP())',
            'iss',
            array(
                $code[0]->authorized_application_id,
                $accessToken->access_token,
                $accessToken->refresh_token,
            ));
        $this->getDatabaseSession()->executeNoResult('DELETE FROM authorization_codes WHERE id = ?', 'i', array($code[0]->id));

        return new EntityResult(true, $accessToken, null);
    }
}
