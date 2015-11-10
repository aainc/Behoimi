<?php

namespace Behoimi\Dao;

use Behoimi\OAuth\AccessToken;
use Behoimi\OAuth\Scopes;

class AccessTokensDao extends \Mahotora\BaseDao
{
    public function getTableName()
    {
        return 'access_tokens';
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT a.authorized_application_id, a.access_token, a.refresh_token, aa.user_id, aa.application_id, s.scope, a.created_at
            FROM access_tokens a, scopes s, authorized_applications aa
            WHERE
            aa.id = a.authorized_application_id AND aa.id = s.authorized_application_id AND
            aa.running = 1 AND a.access_token = ? ', 's', is_array($id) ? $id : array($id)
        );
        $accessToken = null;
        if ($result) {
            $accessToken = new AccessToken($result);
            $accessToken->setAuthorizedApplicationId($result[0]->authorized_application_id);
            $accessToken->setAccessToken($result[0]->access_token);
            $accessToken->setRefreshToken($result[0]->refresh_token);
            $accessToken->setApplicationId($result[0]->application_id);
            $accessToken->setUserId($result[0]->user_id);
            $accessToken->setScopes(new Scopes(array_map(function ($row) {
                return $row->scope;
            }, $result)));
            $accessToken->setCreatedAt($result[0]->created_at);
        }

        return $accessToken;
    }

    public function save($entity)
    {
        parent::save($entity);
        if (!isset($entity->id)) {
            $id = $this->getDatabaseSession()->lastInsertId();
            $entity->id = $id;
        }
    }

    public function delete($id)
    {
        $this->getDatabaseSession()->executeNoResult(
            'DELETE FROM access_tokens WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }
}
