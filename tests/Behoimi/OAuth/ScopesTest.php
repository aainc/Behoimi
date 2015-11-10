<?php
namespace Behoimi\OAuth;


class ScopesTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;

    protected function setUp()
    {
        $this->target = new Scopes(array(1, 2));
    }

    public function testIsAllow()
    {
        $this->assertSame(true, $this->target->isAllow(1));
        $this->assertSame(true, $this->target->isAllow(2));
    }

    public function testIsDeny()
    {
        $this->assertSame(false, $this->target->isAllow(0));
        $this->assertSame(false, $this->target->isAllow(1.1));
        $this->assertSame(false, $this->target->isAllow(1.9));
    }

}
