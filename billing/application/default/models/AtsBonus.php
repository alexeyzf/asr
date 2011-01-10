<?php
/**
 * Model for ats_bonus table
 * 
 * @author marat
 */

require_once('Zend/Db/Table.php');

class AtsBonus extends Zend_Db_Table 
{
	/**
	 * Default table name
	 * @var string
	 */
	protected $_name = 'ats_bonus';
	
	public function add($clientID, $pointID, $startDate, $endDate, $amount, $notes, $userID)
	{
		$data['client_id'] = $clientID;
		$data['point_id'] = $pointID;
		$data['startdate'] = $startDate;
		$data['enddate'] = $endDate;
		$data['amount'] = $amount;
		$data['notes'] = $notes;
		$data['user_id'] = $userID;
		$data['created'] = date('Y-m-d');
		
		return $this->insert($data);
	}
	
	public function getAmount($clientID, $pointID, $startDate, $endDate)
	{
		$sql = $this->_db->select()
			->from($this->_name, array('amount'))
			->where('client_id = ?', $clientID)
			->where('point_id = ?', $pointID)
			->where('created >= ?', $startDate)
			->where('created < ?', $endDate);
		
		return $this->_db->fetchOne($sql);
	}
}