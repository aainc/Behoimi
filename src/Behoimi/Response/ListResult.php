<?php
/**
 * Date: 15/10/02
 * Time: 10:39.
 */
namespace Behoimi\Response;

use Hoimi\Response\Json;
use Behoimi\Pager\PagingUrl;

class ListResult extends JSON
{
    public function __construct($result, array $list, PagingUrl $pagingUrl, $error)
    {
        parent::__construct(array('data' => array(
            'result' => $result,
            'list' => $list,
            'paging' => array(
                'cursor' => array(
                    'prev' => $pagingUrl->getPrev(),
                    'next' => $pagingUrl->getNext(),
                ),
            ),
            'error' => $error,
        )));
    }
}
