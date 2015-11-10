<?php
namespace Behoimi\Action;

use Behoimi\Dao\AccessTokensDao;
use Behoimi\OAuth\InvalidScopeException;
use Behoimi\OAuth\InvalidTokenException;
use Hoimi\Exception\ValidationException;

abstract class BaseOAuthAction extends ApiBaseAction
{
    private $accessToken = null;
    private $accessTokenDao = null;

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @throws \Hoimi\Exception\ValidationException
     */
    public function getAccessToken()
    {
        if ($this->accessToken === null) {
            $token = $this->getRequest()->get('access_token');
            // todo: try get access token from header
            if ($token === null || $token === '') {
                throw new ValidationException(array('access_token' => 'NOT_REQUIRED'));
            }
            $this->accessToken = $this->getAccessTokenDao()->find($token);
            if ($this->accessToken === null || $this->accessToken->isExpired()) {
                throw new InvalidTokenException();
            }
            if (!$this->accessToken->getScopes()->isAllow($this->getScope())) {
                throw new InvalidScopeException();
            }
            $this->getDatabaseSession()->executeNoResult(
                'INSERT INTO `access_token_logs` (`authorized_application_id`, `action_name`, `method_name`, `created_at`)VALUES(?,?,?,UNIX_TIMESTAMP())',
                'iss',
                array(
                    $this->accessToken->getAuthorizedApplicationId(),
                    get_class($this),
                    $this->getRequest()->getMethod(),
                )
            );
        }

        return $this->accessToken;
    }

    public function getScope()
    {
        $method = $this->getRequest()->getMethod();
        if ($method === 'GET' && method_exists($this, 'getGetScope')) {
            return $this->getGetScope();
        } elseif ($method === 'POST' && method_exists($this, 'getPostScope')) {
            return $this->getPostScope();
        } elseif ($method === 'DELETE' && method_exists($this, 'getDeleteScope')) {
            return $this->getDeleteScope();
        } elseif ($method === 'PUT' && method_exists($this, 'getPutScope')) {
            return $this->getPutScope();
        }

        return;
    }

    /**
     * @param null $accessTokenDao
     */
    public function setAccessTokenDao($accessTokenDao)
    {
        $this->accessTokenDao = $accessTokenDao;
    }

    /**
     * @return null
     */
    public function getAccessTokenDao()
    {
        if ($this->accessTokenDao === null) {
            $this->accessTokenDao = new AccessTokensDao($this->getDatabaseSession());
        }
        return $this->accessTokenDao;
    }
}
