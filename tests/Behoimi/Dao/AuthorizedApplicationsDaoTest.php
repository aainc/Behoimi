<?php
namespace Behoimi\Dao;

class AuthorizedApplicationsDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\AuthorizedApplicationsDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "user_id" => 1,
            "application_id" => 1,
            "running" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM authorized_applications WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array($entity));
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM authorized_applications WHERE id = ?",
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
            "user_id" => 1,
            "application_id" => 1,
            "running" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM authorized_applications WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array());
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM authorized_applications WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame(null, $result);
    }
}
