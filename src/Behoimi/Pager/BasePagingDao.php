<?php
/**
 * Date: 15/10/13
 * Time: 16:25.
 */
namespace Behoimi\Dao;

use Mahotora\BaseDao;
use Behoimi\Pager\BasePager;
use Behoimi\Pager\PagingResult;

abstract class BasePagingDao extends BaseDao
{
    protected function findPage($query, $marker, $params, BasePager $Pager)
    {
        return $this->traversePage($query, $marker, $params, $Pager);
    }

    protected function traversePage($query, $marker, $params, BasePager $Pager, \Closure $callable = null)
    {
        $list = $this->getDatabaseSession()->traverse(
            $query.' '.$Pager->buildSQL(),
            $callable,
            $marker,
            $params
        );
        $nextPage = null;
        $prevPage = $Pager->getPage() <= 1 ? null : $Pager->getPage() - 1;
        if (count($list) > $Pager->getLimit()) {
            array_pop($list);
            $nextPage = $Pager->getPage() + 1;
        }

        return $list ?
            new PagingResult($list, $prevPage, $nextPage, $Pager->getLimit(), $Pager->getOrder(), $Pager->getDirection()) :
            null;
    }
}
