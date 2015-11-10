<?php
namespace Behoimi\Dao;

class AccessTokensDaoTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $databaseSession = null;

    public function setUp()
    {
        $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');
        $this->target = new \Behoimi\Dao\AccessTokensDao($this->databaseSession);
    }

    public function testFind()
    {
        $entity = (object)array(
            "id" => 1,
            "application_id" => 1,
            "authorized_application_id" => 1,
            "user_id" => 1,
            "access_token" => "access_token",
            "refresh_token" => "refresh_token",
            "created_at" => 1,
            'scope' => 1,
        );
        $entity2 = (object)array(
            "id" => 1,
            "application_id" => 1,
            "authorized_application_id" => 1,
            "user_id" => 1,
            "access_token" => "access_token",
            "refresh_token" => "refresh_token",
            "created_at" => 1,
            'scope' => 2,
        );
        \Phake::when($this->databaseSession)->find(
            $this->anything(),
            "s",
            array('hoge')
        )->thenReturn(array($entity, $entity2));
        $result = $this->target->find('hoge');
        \Phake::verify($this->databaseSession)->find(
            $this->anything(),
            "s",
            array('hoge')
        );
        $this->assertInstanceOf('Behoimi\OAuth\AccessToken', $result);
        $this->assertSame(1, $result->getUserId());
        $this->assertSame('access_token', $result->getAccessToken());
        $this->assertSame('refresh_token', $result->getRefreshToken());
        $this->assertSame(2, $result->getScopes()->count());
        $this->assertSame(1, $result->getScopes()->get(0));
        $this->assertSame(2, $result->getScopes()->get(1));
    }

    public function testFindNoResult()
    {
        $entity = (object)array(
            "id" => 1,
            "application_id" => 1,
            "authorized_application_id" => 1,
            "user_id" => 1,
            "access_token" => "access_token",
            "refresh_token" => "refresh_token",
            "created_at" => 1,
            'scope' => 1,
        );
        \Phake::when($this->databaseSession)->find($this->anything(), "s", array(1))->thenReturn(array());
        $result = $this->target->find(array(1));
        \Phake::verify($this->databaseSession)->find($this->anything(), "s",  array(1));
        $this->assertSame(null, $result);
    }
}
