<?php
/**
 * Model for recalc_months table
 * 
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class Recalcs extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'recalcs';
	
	public function getRecalculations($clientID, $startDate, $endDate)
	{
		return $this->_db->select()->from(array('r' => 'recalcs'))
			->join(array('p' => 'points'), 'p.point_id = r.point_id', array('u_login'))
			->join(array('oldt' => 'tarifs'), 'oldt.tarif_id = r.old_tarif_id', array('old_tarif' => 'tarif_name'))
			->join(array('newt' => 'tarifs'), 'newt.tarif_id = r.new_tarif_id', array('new_tarif' => 'tarif_name'))
			->join(array('u' => 'users'), 'u.id = r.user_id', array('first_name', 'last_name'))
			->where('r.client_id = ?', $clientID)
			->where("r.created BETWEEN '{$startDate}' AND '{$endDate}'")
			->query()->fetchAll();
	}
	
	public function save($clientID, $sdata, $amount, $userID)
	{
		$data['client_id'] = $clientID;
		$data['point_id'] = $sdata['point_id'];
		$data['old_tarif_id'] = $sdata['old_tarif_id'];
		$data['new_tarif_id'] = $sdata['tarif_id'];
   		$data['old_service_startdate'] = $sdata['old_startdate'];
   		$data['old_service_enddate'] = $sdata['old_enddate'];
   		$data['new_service_startdate'] = $sdata['startdate'];
   		$data['new_service_enddate'] = $sdata['enddate'];
   		$data['total_amount'] = $amount;
   		$data['created'] = date('Y-m-d');
   		$data['user_id'] = $userID;
   		$this->insert($data);
	}
}