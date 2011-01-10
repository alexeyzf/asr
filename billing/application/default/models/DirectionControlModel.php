<?
/**
 * DirectionControlModel
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class DirectionControlModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'direction_control';
	
	public function getTraffic($ips, $startDate, $endDate, $calcTrafficType)
	{
		$ipString = '';
		$connector = '';
		
		foreach ($ips as $ip)
		{
			$ipString .= $connector . "'{$ip}'";
			$connector = ',';
		}
		
		if ( ! $ipString )
		{
			return 0;
		}
	
		$sql = "
			SELECT
    			sum(bytes_in)::real / 1024 / 1024 as traffic_in,
    			sum(bytes_out)::real / 1024 / 1024 as traffic_out
    		FROM
    			data01
    		WHERE
    			ip_address IN (" . $ipString . ")
    			AND date_time >= '{$startDate}'
    			AND date_time < '{$endDate}'
    	";
    	
    	$traffic = $this->_db->fetchRow($sql);
    	
		if ($calcTrafficType == 0)
        {
            return $traffic['traffic_in'];
        }
        elseif ($calcTrafficType == 1)
        {
            if ($traffic['traffic_in'] < 3 * $traffic['traffic_out'])
            {
                return $traffic['traffic_out'] - $traffic['traffic_in'] / 3 + $traffic['traffic_in'];
            }
            else
            {
                return $traffic['traffic_in'];
            }
        }
        elseif ($calcTrafficType == 2)
        {
            if ($traffic['traffic_in'] > $traffic['traffic_out'])
            {
                return $traffic['traffic_in'];
            }
            else
            {
                return $traffic['traffic_out'];
            }
        }
        else
        {
            return $traffic['traffic_in'];
        }
	}
}
