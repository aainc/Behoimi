<?php

namespace Behoimi\Dao;

class AuthorizationCodesDao extends \Mahotora\BaseDao
{
    public function getTableName()
    {
        return 'authorization_codes';
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
            'DELETE FROM authorization_codes WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM authorization_codes WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result ? $result[0] : null;
    }
}
