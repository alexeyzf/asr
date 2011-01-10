<?
/**
 * Model for recalc_tasks table
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class RecalcTasks extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'recalc_tasks';

	public function getHistory($clientID, $startDate, $endDate)
	{
		return $this->fetchAll("client_id = {$clientID} 
								AND is_canceled = false
								AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'");
	}
}
