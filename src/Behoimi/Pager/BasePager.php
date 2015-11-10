<?php
/**
 * Date: 15/10/09
 * Time: 18:39.
 */
namespace Behoimi\Pager;

use Hoimi\Exception\ValidationException;
use Hoimi\Gettable;
use Hoimi\Request;

abstract class BasePager
{
    private $request = null;

    abstract public function getColumns();

    abstract public function getDefaultColumnKey();

    abstract public function getDefaultDirection();

    public function __construct(Gettable $request)
    {
        $validationResult = \Hoimi\Validator::validate($request, array(
            'page' => array('required' => false, 'dataType' => 'integer', 'min' => 1),
            'limit' => array('required' => false, 'dataType' => 'integer', 'min' => 1, 'max' => 1000),
            'order' => array('required' => false, 'dataType' => 'string'),
            'direction' => array('required' => false, 'dataType' => 'string', 'regex' => '#^(?:asc|desc)$#i'),
        ));
        if ($validationResult) {
            throw new ValidationException($validationResult);
        }
        if ($request->get('order')) {
            $list = $this->getColumns();
            if (!isset($list[$request->get('order')])) {
                $message = 'INVALID_FORMAT@^(?:'.implode('|', array_map(function ($row) {
                        return preg_quote($row);
                }, array_keys($list))).')$';
                throw new ValidationException(array('order' => $message));
            }
        }
        $this->request = $request;
    }

    public function buildSQL()
    {
        $columns = $this->getColumns();
        $order = $columns[$this->getOrder()];
        $direction = $this->getDirection();
        $limit = $this->getLimit() + 1;
        $page = $this->getPage();
        $offset = $this->getLimit() * ($page - 1);

        return "ORDER BY $order $direction LIMIT $limit OFFSET $offset";
    }

    public function getDirection()
    {
        return $this->request->get('direction', $this->getDefaultDirection());
    }

    public function getOrder()
    {
        return $this->request->get('order', $this->getDefaultColumnKey());
    }

    public function getPage()
    {
        return $this->request->get('page', 1);
    }

    public function getLimit()
    {
        return $this->request->get('limit', $this->getDefaultCount());
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getDefaultCount()
    {
        return 50;
    }

    public function __toString()
    {
        $this->buildSQL();
    }
}
