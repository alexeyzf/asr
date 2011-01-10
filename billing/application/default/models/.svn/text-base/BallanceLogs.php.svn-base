<?
/**
 * Model for ballance_logs table
 *  
 * @author marat
 */

require_once('Zend/Db/Table.php');
require_once('Rates.php');

class BallanceLogs extends Zend_Db_Table
{
	/**
	 * The default table name 
	 */
	protected $_name = 'ballance_logs';
	
	public function getBallanceOnDate($date, $clientID)
	{
		$date = date('Y-m-d', strtotime($date));
		
		$sql = "
			SELECT
				cur_date,
				ballance
			FROM
				ballance_logs
			WHERE
				cur_date <= '{$date}'
				AND clientid = {$clientID}
			ORDER BY
				cur_date DESC
		";
		
		$ballanceRow = $this->_db->fetchRow($sql);
		$ballanceUsd = $ballanceRow['ballance'];
		
		$rateModel = new Rates(); 
		$rateDate = $ballanceRow['cur_date'] ? $ballanceRow['cur_date'] : $date; 
		$rate = $rateModel->getRate($rateDate);
		
		$ballanceUzs = $ballanceRow['ballance'] * $rate;
		
		return array($ballanceUsd, $ballanceUzs);
	}
	
	public function getBallanceLogs($date)
	{
		$sql = "
			SELECT
				clientid, 
				MAX(ballance) AS ballance
			FROM
				ballance_logs
			WHERE
				cur_date = '{$date}'
			GROUP BY clientid
		";
		
		$data = $this->_db->fetchAll($sql);
		$result = array();
		
		foreach ($data as $row)
		{
			$result[$row['clientid']] = $row['ballance'];
		}
		
		return $result;
	}
}
