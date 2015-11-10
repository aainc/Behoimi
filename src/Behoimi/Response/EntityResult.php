<?php
/**
 * Date: 15/10/02
 * Time: 10:38.
 */
namespace Behoimi\Response;

use Hoimi\Response\Json;

class EntityResult extends JSON
{
    public function __construct($result, $entity, $error)
    {
        parent::__construct(array('data' => array('result' => $result, 'entity' => $entity, 'error' => $error)));
    }
}
