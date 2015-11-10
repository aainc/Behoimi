<?php
namespace Behoimi\Pager;



use Hoimi\Request;

class PagingUrlTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;

    protected function setUp()
    {
        $request = new Request(array(
            'REQUEST_URI' => '/hoge?fuga=piyo&limit=50&page=2',
            'SERVER_NAME' => 'localhost',
            'HTTPS' => 'on',
        ), array(), array());
        $result = new PagingResult(array(), 1, 3, 50, 'orderColumn', 'ASC');
        $this->target = new PagingUrl($request, $result);
    }

    public function testGetNext ()
    {
        $this->assertSame('https://localhost/hoge?fuga=piyo&page=3&limit=50&order=orderColumn&direction=ASC', $this->target->getNext());
    }

    public function testGetPrev ()
    {
        $this->assertSame('https://localhost/hoge?fuga=piyo&page=1&limit=50&order=orderColumn&direction=ASC', $this->target->getPrev());
    }
}
