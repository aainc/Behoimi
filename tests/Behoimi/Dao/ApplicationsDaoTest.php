<?php
namespace Behoimi\Dao;

class ApplicationsDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\ApplicationsDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "name" => "name",
            "client_id" => "client_id",
            "client_secret" => "client_secret",
            "application_type" => 1,
            "redirect_uri" => "redirect_uri",
            "withdraw_uri" => "withdraw_uri",
            "deleted_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM applications WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array($entity));
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM applications WHERE id = ?",
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
            "name" => "name",
            "client_id" => "client_id",
            "client_secret" => "client_secret",
            "application_type" => 1,
            "redirect_uri" => "redirect_uri",
            "withdraw_uri" => "withdraw_uri",
            "deleted_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM applications WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array());
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM applications WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame(null, $result);
    }
}
