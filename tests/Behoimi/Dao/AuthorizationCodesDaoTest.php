<?php
namespace Behoimi\Dao;

class AuthorizationCodesDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\AuthorizationCodesDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "aturhorized_application_id" => 1,
            "code" => "code",
            "expired_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM authorization_codes WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array($entity));
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM authorization_codes WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame($entity, $result);
    }

    public function testFindNoResult()
    {
        $entity = (object)array(
            "id" => 1,
            "aturhorized_application_id" => 1,
            "code" => "code",
            "expired_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM authorization_codes WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array());
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM authorization_codes WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame(null, $result);
    }
}
