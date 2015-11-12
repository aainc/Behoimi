<?php
/**
 * Date: 15/10/14
 * Time: 11:39
 */

namespace Behoimi\Pager;


use Hoimi\ArrayContainer;

class BasePagingDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DummyPagingDao
     */
    private $target = null;
    private $databaseSession = null;

    public function setUp ()
    {
        $this->databaseSession = \Phake::mock('\Mahotora\DatabaseSessionImpl');
        $this->target = new DummyPagingDao($this->databaseSession);
    }

    public function testTraversePageNext ()
    {
        \Phake::when($this->databaseSession)->traverse(\Phake::anyParameters())->thenReturn(array(1, 2, 3, 4));
        $result = $this->target->paging(new DummyPagerForPagingDao(new ArrayContainer(array('limit' => 3, 'page' => 3))));
        $this->assertSame(4, $result->getNext());
        $this->assertSame(2, $result->getPrev());
        $this->assertSame('ASC', $result->getDirection());
        $this->assertSame('col5Key', $result->getOrder());
        $this->assertSame(3, $result->getCount());
    }

    public function testTraversePageNoNext ()
    {
        \Phake::when($this->databaseSession)->traverse(\Phake::anyParameters())->thenReturn(array(1, 2, 3));
        $result = $this->target->paging(new DummyPagerForPagingDao(new ArrayContainer(array('limit' => 3, 'page' => 3))));
        $this->assertSame(null, $result->getNext());
        $this->assertSame(2, $result->getPrev());
        $this->assertSame('ASC', $result->getDirection());
        $this->assertSame('col5Key', $result->getOrder());
        $this->assertSame(3, $result->getCount());
    }

    public function testTraversePageNoPrev ()
    {
        \Phake::when($this->databaseSession)->traverse(\Phake::anyParameters())->thenReturn(array(1, 2, 3));
        $result = $this->target->paging(new DummyPagerForPagingDao(new ArrayContainer(array('limit' => 3, 'page' => 1))));
        $this->assertSame(null, $result->getNext());
        $this->assertSame(null, $result->getPrev());
        $this->assertSame('ASC', $result->getDirection());
        $this->assertSame('col5Key', $result->getOrder());
        $this->assertSame(3, $result->getCount());
    }

}

class DummyPagingDao extends BasePagingDao
{
    public function getTableName()
    {
        return 'dummy';
    }

    public function paging(BasePager $pager)
    {
        return $this->traversePage('SELECT * FROM dummy', null, array(), $pager, function ($row) {
            return $row;
        });
    }
    public function find($id)
    {
        return $this->getDatabaseSession()->find(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?',
            'i' ,
            array($id)
        );
    }
}

class DummyPagerForPagingDao extends BasePager
{
    public function getColumns()
    {
        return array (
            'col1Key' => 'col1Name',
            'col2Key' => 'col2Name',
            'col3Key' => 'col3Name',
            'col4Key' => 'col4Name',
            'col5Key' => 'col5Name',
        );
    }

    public function getDefaultColumnKey()
    {
        return 'col5Key';
    }

    public function getDefaultDirection()
    {
        return 'ASC';
    }
}
