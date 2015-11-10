<?php
/**
 * Date: 15/10/01
 * Time: 20:48.
 */
namespace Behoimi\response;

use Hoimi\Response\Json;

class NoResult extends Json
{
    public function __construct($result, $error)
    {
        parent::__construct(array('data' => array('result' => $result, 'error' => $error)));
    }
}
