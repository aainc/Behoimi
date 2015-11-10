<?php
namespace Behoimi\Dao;

class ScopesDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\ScopesDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "authorized_application_id" => 1,
            "scope" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM scopes WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array($entity));
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM scopes WHERE id = ?",
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
            "authorized_application_id" => 1,
            "scope" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM scopes WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array());
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM scopes WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame(null, $result);
    }
}
