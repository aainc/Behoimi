<?php

namespace Behoimi\Dao;

class ScopesDao extends \Mahotora\BaseDao
{
    public function getTableName()
    {
        return 'scopes';
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
            'DELETE FROM scopes WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM scopes WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result ? $result[0] : null;
    }

    public function findByAuthorizedAppId($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM scopes WHERE authorized_application_id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result;
    }
}
