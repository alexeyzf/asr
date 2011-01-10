<?
/**
 * StreamTrafficModel
 *  
 * @author marat 
 */

require_once 'Zend/Db/Table/Abstract.php';

class StreamTrafficModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'stream_traffic';
	
	public function getStreamTraffic($userName, $startDate, $endDate)
    {
    	$sql = "
    		SELECT
    			traffic_in::real / 1024 / 1024 as traffic_in,
    			traffic_out::real / 1024 / 1024 as traffic_out
    		FROM
    			stream_traffic
    		WHERE
    			username = '{$userName}'
    			AND date >= '{$startDate}'
    			AND date < '{$endDate}'
    	";
    	
    	$traffic = $this->_db->fetchRow($sql);
    	
    	if ($traffic['traffic_in'] > $traffic['traffic_out'])
    	{
    		return $traffic['traffic_in'];
    	}
    	else
    	{
    		return $traffic['traffic_out'];
    	}
    }
}
