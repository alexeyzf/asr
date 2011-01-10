<?php

require_once ('Zend/Db/Table/Abstract.php');

class ClientServicesHistoryModel extends Zend_Db_Table_Abstract 
{
	protected $_name = 'client_services_history';
	
	public function getServicesStartDate($clientID)
	{
		$sql = $this->_db->select()
			->from($this->_name, array('startdate'))
			->where('client_id = ?', $clientID)
			->where('is_forced = true')
			->order('startdate')
			->limit(1);
		
		return $this->_db->fetchOne($sql);
	}
}
