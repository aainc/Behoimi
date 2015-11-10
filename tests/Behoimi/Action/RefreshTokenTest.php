<?php
namespace Behoimi\Action;

use Hoimi\Exception\ValidationException;
use Hoimi\Request;
use Zaolik\DIContainer;

class RefreshTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RefreshToken
     */
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $databaseSession = $this->databaseSession = \Phake::mock('\Mahotora\DatabaseSessionImpl');
        DIContainer::getInstance()->clear();
        DIContainer::getInstance()->setFlyWeight('databaseSession', function() use ($databaseSession) {
            return $databaseSession;
        });
        $this->target = new RefreshToken();
        $this->target->setContainer(DIContainer::getInstance());
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testGetNoParameter()
    {
        $this->target->setRequest(new Request(
            array(),
            array('refresh_token' => null),
            array()
        ));
        $this->target->get();
    }

    /**
     * @expectedException \Behoimi\OAuth\InvalidRefreshTokenException
     */
    public function testInvalidRefreshToken()
    {
        \Phake::when($this->databaseSession)->executeNoResult(
            "UPDATE access_tokens SET access_token = ?, created_at = UNIX_TIMESTAMP() WHERE refresh_token = ?",
            'ss',
            $this->anything()
        )->thenReturn(0);
        $this->target->setRequest(new Request(
            array(),
            array('refresh_token' => 'hogefuga'),
            array()
        ));
        $this->target->get();
    }

    public function testOK()
    {
        \Phake::when($this->databaseSession)->executeNoResult(
            "UPDATE access_tokens SET access_token = ?, created_at = UNIX_TIMESTAMP() WHERE refresh_token = ?",
            'ss',
            $this->anything()
        )->thenReturn(1);
        $this->target->setRequest(new Request(
            array(),
            array('refresh_token' => 'hogefuga'),
            array()
        ));
        $result = $this->target->get();
        $data = $result->getData();
        $this->assertSame(50, strlen($data['data']['entity']->access_token));
        $this->assertSame('hogefuga', $data['data']['entity']->refresh_token);
        $this->assertSame(900, $data['data']['entity']->expired_in);
        $this->assertSame(true, $data['data']['result']);
        $this->assertSame(null, $data['data']['error']);
    }

}
