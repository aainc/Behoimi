<?php
namespace Behoimi\Action;


use Behoimi\OAuth\AccessToken;
use Behoimi\OAuth\Scopes;
use Hoimi\Request;
use Zaolik\DIContainer;

class BaseOAuthActionTest extends \PHPUnit_Framework_TestCase
{
    private $databaseSession = null;
    private $get = null;
    private $post = null;
    private $put = null;
    private $delete = null;
    private $getRequest = null;
    private $postRequest = null;
    private $putRequest = null;
    private $deleteRequest = null;
    private $emptyGetRequest = null;
    private $accessTokensDao = null;
    private $accessTokenLogsDao = null;
    private $accessToken = null;
    protected function setUp()
    {
        $this->get = new DummyOAuthActionGET();
        $this->post = new DummyOAuthActionPOST();
        $this->put = new DummyOAuthActionPUT();
        $this->delete = new DummyOAuthActionDELETE();
        $this->emptyGetRequest = new Request(array('REQUEST_METHOD' => 'GET'), array('access_token' => null), array());
        $this->getRequest = new Request(array('REQUEST_METHOD' => 'GET'), array('access_token' => 'hoge'), array());
        $this->postRequest = new Request(array('REQUEST_METHOD' => 'POST'), array('access_token' => 'hoge' ), array());
        $this->deleteRequest = new Request(array('REQUEST_METHOD' => 'DELETE'), array('access_token' => 'hoge'), array());
        $this->putRequest = new Request(array('REQUEST_METHOD' => 'PUT'), array('access_token' => 'hoge'), array());
        DIContainer::getInstance()->clear();
        $accessTokensDao = $this->accessTokensDao = \Phake::mock('Behoimi\Dao\AccessTokensDao');
        $accessTokenLogsDao = $this->accessTokenLogsDao = \Phake::mock('Behoimi\Dao\AccessTokenLogsDao');
        $databaseSession = $this->databaseSession = \Phake::mock('Mahotora\DatabaseSessionImpl');

        DIContainer::getInstance()->setFlyWeight('databaseSession', function () use ($databaseSession){
            return $databaseSession;
        })->setNew('now',function(){
            return 9999;
        });

        $this->get->setAccessTokenDao($accessTokensDao);
        $this->post->setAccessTokenDao($accessTokensDao);
        $this->put->setAccessTokenDao($accessTokensDao);
        $this->delete->setAccessTokenDao($accessTokensDao);

        $this->get->setRequest($this->getRequest);
        $this->post->setRequest($this->postRequest);
        $this->put->setRequest($this->putRequest);
        $this->delete->setRequest($this->deleteRequest);
        $this->get->setContainer(DIContainer::getInstance());
        $this->post->setContainer(DIContainer::getInstance());
        $this->put->setContainer(DIContainer::getInstance());
        $this->delete->setContainer(DIContainer::getInstance());
        $this->accessToken = new AccessToken();
        $this->accessToken->setAuthorizedApplicationId(999);
        $this->accessToken->setUserId(1234);
        $this->accessToken->setScopes(new Scopes(array(Scopes::R_PROFILE)));
        $this->accessToken->setCreatedAt(time());
    }

    public function testGetScopeGET()
    {
        $this->get->setRequest($this->getRequest);
        $this->assertSame(Scopes::R_PROFILE, $this->get->getScope());
        $this->get->setRequest($this->postRequest);
        $this->assertSame(null, $this->get->getScope());
        $this->get->setRequest($this->deleteRequest);
        $this->assertSame(null, $this->get->getScope());
        $this->get->setRequest($this->putRequest);
        $this->assertSame(null, $this->get->getScope());
    }

    public function testPostScopeGET()
    {
        $this->post->setRequest($this->getRequest);
        $this->assertSame(null, $this->post->getScope());
        $this->post->setRequest($this->postRequest);
        $this->assertSame(Scopes::W_PROFILE, $this->post->getScope());
        $this->post->setRequest($this->deleteRequest);
        $this->assertSame(null, $this->post->getScope());
        $this->post->setRequest($this->putRequest);
        $this->assertSame(null, $this->post->getScope());
    }

    public function testPutScopeGET()
    {
        $this->put->setRequest($this->getRequest);
        $this->assertSame(null, $this->put->getScope());
        $this->put->setRequest($this->postRequest);
        $this->assertSame(null, $this->put->getScope());
        $this->put->setRequest($this->deleteRequest);
        $this->assertSame(null, $this->put->getScope());
        $this->put->setRequest($this->putRequest);
        $this->assertSame(Scopes::W_PROFILE, $this->put->getScope());
    }

    public function testDeleteScopeGET()
    {
        $this->delete->setRequest($this->getRequest);
        $this->assertSame(null, $this->delete->getScope());
        $this->delete->setRequest($this->postRequest);
        $this->assertSame(null, $this->delete->getScope());
        $this->delete->setRequest($this->deleteRequest);
        $this->assertSame(Scopes::W_PROFILE, $this->delete->getScope());
        $this->delete->setRequest($this->putRequest);
        $this->assertSame(null, $this->delete->getScope());
    }

    public function testGetAccessTokens()
    {
        \Phake::when($this->accessTokensDao)->find('hoge')->thenReturn($this->accessToken);
        $accessToken = $this->get->getAccessToken();
        $this->assertSame($this->accessToken, $accessToken);
        \Phake::verify($this->databaseSession)->executeNoResult(
            "INSERT INTO `access_token_logs` (`authorized_application_id`, `action_name`, `method_name`, `created_at`)VALUES(?,?,?,UNIX_TIMESTAMP())",
            'iss',
            array(
                 999,
                 'Behoimi\Action\DummyOAuthActionGET',
                 'GET',
            ));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testGetAccessTokenNone()
    {
        $this->get->setRequest($this->emptyGetRequest);
        $accessToken = $this->get->getAccessToken();
    }

    /**
     * @expectedException \Behoimi\OAuth\InvalidTokenException
     */
    public function testGetAccessTokenInvalid()
    {
        \Phake::when($this->accessTokensDao)->find('fuga')->thenReturn($this->accessToken);
        $accessToken = $this->get->getAccessToken();
    }

    /**
     * @expectedException \Behoimi\OAuth\InvalidScopeException
     */
    public function testGetAccessTokenInvalidScope()
    {
        \Phake::when($this->accessTokensDao)->find('hoge')->thenReturn($this->accessToken);
        $accessToken = $this->post->getAccessToken();
    }

}

class DummyOAuthActionGET extends BaseOAuthAction
{
    public function getGETScope ()
    {
        return Scopes::R_PROFILE;
    }

}
class DummyOAuthActionPOST extends BaseOAuthAction
{
    public function getPOSTScope ()
    {
        return Scopes::W_PROFILE;
    }
}
class DummyOAuthActionDELETE extends BaseOAuthAction
{
    public function getDELETEScope ()
    {
        return Scopes::W_PROFILE;
    }
}
class DummyOAuthActionPUT extends BaseOAuthAction
{
    public function getPUTScope ()
    {
        return Scopes::W_PROFILE;
    }
}
