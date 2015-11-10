<?php
namespace Behoimi\Response;


class NoResultTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    protected function setUp()
    {
        $this->target = new NoResult(true, null);
    }

    public function testOne()
    {
        $this->assertEquals(
            '{"data":{"result":true,"error":null}}',
            $this->target->getContent()
        );
    }
}
