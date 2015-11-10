<?php
namespace Behoimi\Response;


class ListResultTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $paginUrl = null;
    protected function setUp()
    {
        $this->paginUrl = \Phake::mock('Behoimi\Pager\PagingUrl');
        \Phake::when($this->paginUrl)->getPrev()->thenReturn('hoge');
        \Phake::when($this->paginUrl)->getNext()->thenReturn('fuga');
        $this->target = new ListResult(true,
            array((object)array('id' => 1, 'name' => 'name')),
            $this->paginUrl,
            null);
    }

    public function testOne()
    {
        $this->assertEquals(
            '{"data":{"result":true,"list":[{"id":1,"name":"name"}],"paging":{"cursor":{"prev":"hoge","next":"fuga"}},"error":null}}',
            $this->target->getContent()
        );
    }
}
