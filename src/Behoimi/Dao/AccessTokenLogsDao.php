<?php

namespace Behoimi\Dao;

class AccessTokenLogsDao extends \Mahotora\BaseDao
{
    public function getTableName()
    {
        return 'access_token_logs';
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
            'DELETE FROM access_token_logs WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM access_token_logs WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result ? $result[0] : null;
    }
}
