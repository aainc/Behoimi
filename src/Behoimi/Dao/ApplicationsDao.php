<?php

namespace Behoimi\Dao;

class ApplicationsDao extends \Mahotora\BaseDao
{

    public function getTableName()
    {
        return 'applications';
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
            'DELETE FROM applications WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );
    }

    public function find($id)
    {
        $result = $this->getDatabaseSession()->find(
            'SELECT * FROM applications WHERE id = ?',
            'i',
            is_array($id) ? $id : array($id)
        );

        return $result ? $result[0] : null;
    }
}
