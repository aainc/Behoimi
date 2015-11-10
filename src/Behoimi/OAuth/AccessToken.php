<?php
namespace Behoimi\OAuth;

class AccessToken
{
    private $authorizedApplicationId = null;
    private $accessToken = null;
    private $refreshToken = null;
    private $applicationId = null;
    private $scopes = null;
    private $userId = null;
    private $createdAt = null;
    const EXPIRED_IN = 900;

    /**
     * @param null $authorizedApplicationId
     */
    public function setAuthorizedApplicationId($authorizedApplicationId)
    {
        $this->authorizedApplicationId = $authorizedApplicationId;
    }

    /**
     */
    public function getAuthorizedApplicationId()
    {
        return $this->authorizedApplicationId;
    }

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param null $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param Scopes $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return Scopes
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param null $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param null $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function isExpired()
    {
        return self::EXPIRED_IN + $this->createdAt < time();
    }
}
