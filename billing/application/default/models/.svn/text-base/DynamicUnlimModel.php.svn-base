<?php
require_once('Zend/Db/Table.php');

class DynamicUnlimModel extends Zend_Db_Table
{
    protected $_name = 'tarif_properties';
    protected $_sequence = 'tarif_properties_seq';

  	public function getSpeedAndPrice($sid)
  	{
		$sql = "
			select (speed_up || '/' || speed_down) as speed, tarif_price from tarif_properties where service_id = {$sid} limit 1
		";
		return $this->_db->fetchRow($sql);
  	}

  	public function getSpeedSeparate($sid)
  	{
		$sql = "
			select speed_up, speed_down from tarif_properties where service_id = {$sid} limit 1
		";
		return $this->_db->fetchRow($sql);
  	}

}
