<?php
namespace Behoimi\Response;


class EntityResultTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    protected function setUp()
    {
        $this->target = new EntityResult(true, (object)array('id' => 1, 'name' => 'name'), null);
    }

    public function testOne()
    {
        $this->assertEquals(
            '{"data":{"result":true,"entity":{"id":1,"name":"name"},"error":null}}',
            $this->target->getContent()
        );
    }
}