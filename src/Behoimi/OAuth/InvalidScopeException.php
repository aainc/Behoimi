<?php
/**
 * Date: 15/10/07
 * Time: 16:44.
 */
namespace Behoimi\OAuth;

use Hoimi\BaseException;
use Hoimi\Response\Json;

class InvalidScopeException extends BaseException
{
    public function buildResponse()
    {
        return new Json(array(
            'error' => 'insufficient_scope',
            'error_description' => 'insufficient_scope',
        ), array(
            'HTTP/1.1 403 forbidden',
            'WWW-Authenticate: Bearer realm="ReFUEL4 API",error="insufficient_scope", error_description="this access token has no scope for this api"',
        ));
    }
}
