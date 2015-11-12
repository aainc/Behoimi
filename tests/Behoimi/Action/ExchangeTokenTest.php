<?php
namespace Behoimi\Action;

use Hoimi\Exception\ValidationException;
use Hoimi\Request;
use Zaolik\DIContainer;

class ExchangeTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExchangeToken
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
        $this->target = new ExchangeToken();
        $this->target->setContainer(DIContainer::getInstance());
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testGetNoParameter()
    {
        $this->target->setRequest(new Request(
            array(),
            array('code' => null),
            array()
        ));
        $this->target->get();
    }

    /**
     * @expectedException \Behoimi\OAuth\InvalidCodeException
     */
    public function testInvalidCode ()
    {
        $this->target->setRequest(new Request(
            array(),
            array('code' => 'hogefuga'),
            array()
        ));

        \Phake::when($this->databaseSession)->find(
            "SELECT id, authorized_application_id FROM authorization_codes WHERE code = ?",
            's',
            array('hogefuga')
        )->thenReturn(array());
        $this->target->get();
    }

    public function testOK ()
    {
        $this->target->setRequest(new Request(
            array(),
            array('code' => 'hogefuga'),
            array()
        ));

        \Phake::when($this->databaseSession)->find(
            "SELECT id, authorized_application_id FROM authorization_codes WHERE code = ?",
            's',
            array('hogefuga')
        )->thenReturn(array((object)array("id" => 1, "authorized_application_id" => 2)));
        $result = $this->target->get();
        $data = $result->getData();
        $this->assertSame(true, $data['data']['result']);
        $this->assertSame(null, $data['data']['error']);
        $this->assertSame(50, strlen($data['data']['entity']->access_token));
        $this->assertSame(50, strlen($data['data']['entity']->refresh_token));
        $this->assertSame(900, $data['data']['entity']->expired_in);
        \Phake::verify($this->databaseSession)->executeNoResult(
            "DELETE FROM authorization_codes WHERE expired_at < UNIX_TIMESTAMP()"
        );
        \Phake::verify($this->databaseSession)->executeNoResult(
            "INSERT INTO access_tokens (authorized_application_id, access_token, refresh_token, created_at) VALUES (?, ?, ?, UNIX_TIMESTAMP())",
            'iss',
            $this->anything()
        );
        \Phake::verify($this->databaseSession)->executeNoResult(
            "DELETE FROM authorization_codes WHERE id = ?",
            'i',
            array(1)
        );
    }

}
