<?php
/**
 * Date: 15/10/13
 * Time: 16:33.
 */
namespace Behoimi\Pager;

class PagingResult
{
    private $list = null;
    private $prev = null;
    private $next = null;
    private $count = null;
    private $order = null;

    public function __construct($list, $prevPage, $nextPage, $count, $order, $direction)
    {
        $this->list = $list;
        $this->prev = $prevPage;
        $this->next = $nextPage;
        $this->count = $count;
        $this->order = $order;
        $this->direction = $direction;
    }

    /**
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     */
    public function getPrev()
    {
        return $this->prev;
    }
}
