<?php
/**
 * Date: 15/10/13
 * Time: 16:25.
 */
namespace Behoimi\Pager;

use Mahotora\BaseDao;
use Behoimi\Pager\BasePager;
use Behoimi\Pager\PagingResult;

abstract class BasePagingDao extends BaseDao
{
    protected function findPage($query, $marker, $params, BasePager $pager)
    {
        return $this->traversePage($query, $marker, $params, $pager);
    }

    protected function traversePage($query, $marker, $params, BasePager $pager, \Closure $callable = null)
    {
        $list = $this->getDatabaseSession()->traverse(
            $query.' '.$pager->buildSQL(),
            $callable,
            $marker,
            $params
        );
        $nextPage = null;
        $prevPage = $pager->getPage() <= 1 ? null : $pager->getPage() - 1;
        if (count($list) > $pager->getLimit()) {
            array_pop($list);
            $nextPage = $pager->getPage() + 1;
        }

        return $list ?
            new PagingResult($list, $prevPage, $nextPage, $pager->getLimit(), $pager->getOrder(), $pager->getDirection()) :
            null;
    }
}
