<?php
/**
 * Date: 15/10/07
 * Time: 16:41.
 */
namespace Behoimi\OAuth;

use Hoimi\Response\Json;

class InvalidTokenException extends \Hoimi\BaseException
{
    public function buildResponse()
    {
        return new Json(array(
            'error' => 'invalid token',
            'error_description' => 'access_token invalid or expired',
        ), array(
            'HTTP/1.1 401 Unauthorized',
            'WWW-Authenticate: Bearer realm="ReFUEL4 API",error="invalid_token", error_description="The access token invalid or expired"',
        ));
    }
}
