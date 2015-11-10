<?php

namespace Behoimi\Dao;

class AuthorizedApplicationsDao extends \Mahotora\BaseDao
{
    public function getTableName()
    {
        return 'authorized_applications';
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
            'DELETE FROM authorized_applications WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM authorized_applications WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result ? $result[0] : null;
    }

    public function findByAppIdAndUserId($appId, $userId)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM authorized_applications WHERE application_id = ? AND user_id = ?',
            'ii',
            array($appId, $userId)
        );

        return $result ? $result[0] : null;
    }
}
