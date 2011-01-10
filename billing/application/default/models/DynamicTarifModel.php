<?php
require_once 'Zend/Db/Table/Abstract.php';

class DynamicTarifModel extends Zend_Db_Table_Abstract
{
    protected $_name     = 'tarif_properties';
    protected $_sequence = 'tarif_properties_seq';

	public function getLastServiceIDByUsername($username)
	{
		$sql = "
			select service_id from client_future_services  where u_login = '{$username}'
		";
		return $this->_db->fetchOne($sql);
	}

    public function insertDynamicOptionsRow($speedUp, $speedDown, $tarifPrice, $username, $sid)
    {

       $sql = "
          insert into tarif_properties(service_id, speed_up, speed_down, tarif_price, username)
          values
          (
               {$sid},
               '{$speedUp}',
               '{$speedDown}',
               {$tarifPrice},
               '{$username}'
          )
       ";
       $this->_db->fetchAll($sql);

       return 1;
    }

    public function getDynamicData($username, $sid)
    {
    	$sql = "
			select * from tarif_properties where service_id = {$sid} limit 1
		";

		return $this->_db->fetchRow($sql);
    }

}
