<?php
require_once('Zend/Db/Table.php');

class OverdraftModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    public function setOverdraft($client_id, $day, $over_type)
    {
		$sql = "
		update clients set
			overdraft 	   = {$day},
			overdraft_type = {$over_type},
			ballance_change = null
		where
			client_id = {$client_id}
		and
			client_type_id = 0
		";

		$this->_db->fetchAll($sql);
    }

    public function deleteOverdraft($client_id)
    {
		$sql =
		"
			update clients set overdraft = 0
			where
				client_id = {$client_id}
		";
		$this->_db->fetchAll($sql);
    }

}