<?
/**
 * RevisionActBallance
 * 
 * Model for revision_act_ballance table 
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class RevisionActBallance extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'revision_act_ballance';
	
	public function logBallance($clientID, $year, $ballanceUsd, $ballanceUzs)
	{
		$data['client_id'] = $clientID;
		$data['year'] = $year;
		$data['ballance_usd'] = $ballanceUsd;
		$data['ballance_uzs'] = $ballanceUzs;
		
		$this->insert($data);
	}
}
