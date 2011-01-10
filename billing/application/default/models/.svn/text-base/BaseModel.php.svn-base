<?php
/**
 * Base class for models
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class BaseModel extends Zend_Db_Table
{
    /**
     * Fetch all records which are not deleted
     *
     * @param string $where - Filter condition
     * @param string $order - Order columns
     * @return array
     */
    public function fetchAllNotDeleted($where = '', $order = NULL)
    {
        $condition = 'is_deleted = false';

        if ( trim($where) )
        {
            $condition .= ' AND ' . $where;
        }

        return $this->fetchAll($condition, $order);
    }

    /**
     * Fetches one record from table by record ID
     *
     * @param int $id - Record ID
     * @return Zend_Db_Table_Row_Abstract
     */
    public function fetchRecordByID($id)
    {
        $id = intval($id);

        if ( ! $id )
        {
            return $this->createRow();
        }

        return $this->fetchRow("id = {$id}");
    }

    /**
     * Marks reocrd to delete. Sets is_deleted flag to true
     *
     * @param int $id - Record ID
     */
    public function markToDelete($id)
    {
        $id = intval($id);

        if ( ! $id )
        {
            return;
        }

        $data['is_deleted'] = true;
        $this->update($data, "id = {$id}");
    }
    
    public function deleteByID($id)
    {
    	$id = intval($id);

        if ( ! $id )
        {
            return;
        }
        
        $this->delete("id = {$id}");
    }
    

    /**
     * Saves changes to db
     *
     * @param array $data - Associative column => $value array
     * @param int $id - Record ID
     */
    public function saveChanges($data, $id = NULL)
    {
        $id = intval($id);

        $columns = $this->_getCols();

        foreach ($data as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($data[$key]);
            }
        }

        if ($id)
        {
            $this->update($data, "id = {$id}");
        }
        else
        {
            unset($data['id']);
            $id = $this->insert($data);
        }

        return $id;
    }
    
    public function getLastInsertID()
    {
    	return $this->_db->lastInsertId($this->_name, $this->_primary);
    }

    public function startTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_db->commit();
    }

    public function rollbackTransaction()
    {
        $this->_db->rollBack();
    }
}