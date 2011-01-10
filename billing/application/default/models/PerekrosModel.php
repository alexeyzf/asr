<?php
require_once('Zend/Db/Table.php');

class PerekrosModel extends Zend_Db_Table
{
    protected $_name = 'moving_demands';
    protected $_sequence = 'moving_demands_seq';

    public function insertData($data)
    {
        if($data['new_pcross'] == "")
        {
            return null;
        }

        $sql = "
            insert into moving_demands(pcross, new_pcross)
            values
            (
                '{$data['pcross_old']}',
                '{$data['new_pcross']}'
            )
        ";
        $this->_db->fetchAll($sql);
    }

    public function getData()
    {
        $sql = "
            select
                MD.*
            from moving_demands as MD
            where
                MD.is_done = false
        ";
        return $this->_db->fetchAll($sql);
    }

    public function markDoneID($id)
    {
        $sql = "
            update moving_demands set is_done = true where id = {$id}
        ";
        $this->_db->fetchAll($sql);
    }
}