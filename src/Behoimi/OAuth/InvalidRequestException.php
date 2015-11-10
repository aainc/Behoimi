<?php
/**
 * Date: 15/10/07
 * Time: 17:32.
 */
namespace Behoimi\OAuth;

use Hoimi\BaseException;
use Hoimi\Response\Json;

class InvalidRequestException extends BaseException
{
    public function buildResponse()
    {
        return new Json(array(
            'error' => 'invalid_request',
            'error_description' => 'tried to access invalid resource.',
        ), array(
            'HTTP/1.1 403 Unauthorized',
            'WWW-Authenticate: Bearer realm="ReFUEL4 API",error="invalid_request", error_description="tried to access invalid resource."',
        ));
    }
}
