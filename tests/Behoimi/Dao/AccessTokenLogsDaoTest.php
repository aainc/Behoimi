<?php
namespace Behoimi\Dao;

class AccessTokenLogsDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\AccessTokenLogsDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "access_token_id" => 1,
            "action_name" => "action_name",
            "method_name" => "method_name",
            "logs" => "logs",
            "created_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM access_token_logs WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array($entity));
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM access_token_logs WHERE id = ?",
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
            "access_token_id" => 1,
            "action_name" => "action_name",
            "method_name" => "method_name",
            "logs" => "logs",
            "created_at" => 1,
        );
        \Phake::when($this->databaseSession)->find(
            "SELECT * FROM access_token_logs WHERE id = ?",
            "i",
            array(
                1,
            )        )->thenReturn(array());
        $result = $this->target->find(array(
            1,
        ));
        \Phake::verify($this->databaseSession)->find(
            "SELECT * FROM access_token_logs WHERE id = ?",
            "i",
            array(
                1,
            )
        );
        $this->assertSame(null, $result);
    }
}
