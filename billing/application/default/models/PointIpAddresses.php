<?php
/**
 * Model for point_ip_addresses table
 *
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class PointIpAddresses extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name     = 'point_ip_addresses';
	protected $_sequence = 'point_ip_addresses_seq';

	public function setEndDate($pointID, $ip, $endDate = 'now')
	{
		$data['end_date'] = date('Y-m-d', strtotime($endDate));
		$this->update($data, "point_id = {$pointID} AND ip_address = '{$ip}'");
	}

	public function getPointIpAddresses($pointID)
	{
		$sql = "
        	SELECT
        		ip_address
        	FROM
        		point_ip_addresses
        	WHERE
        		point_id = {$pointID}
        		AND now() BETWEEN start_date AND end_date
        ";

        return $this->_db->fetchCol($sql);
	}
	
	public function getPointIpAddressesOnDate($pointID, $date)
	{
		$sql = "
        	SELECT
        		ip_address
        	FROM
        		point_ip_addresses
        	WHERE
        		point_id = {$pointID}
        		AND '{$date}' > start_date 
        		AND '{$date}' < end_date
        ";

        return $this->_db->fetchCol($sql);
	}
	
	public function getIpHistory($pointID, $startDate, $endDate)
	{
		$result = $this->fetchAll("point_id = {$pointID} 
			AND start_date < '{$endDate}'
			AND end_date > '{$startDate}'");
		
		if ($result)
		{
			return $result->toArray();
		}
		else
		{
			return array();
		}
	}

	public function getPointIpAddressesString($pointID)
	{
		$ips = $this->getPointIpAddresses($pointID);

		if (is_array($ips))
		{
			return implode(' ', $ips);
		}
		else
		{
			return '';
		}
	}

	public function getPointByIpAddress($ipAddress)
	{
		$sql = "
			SELECT
				point_id
			FROM
				point_ip_addresses
        	WHERE
        		ip_address = '{$ipAddress}'
        		AND now() BETWEEN start_date AND end_date
		";

		return $this->_db->fetchOne($sql);
	}

	public function addIP($pointID, $ip)
	{
		if($ip == "")
		{
			return;
		}

		$data['point_id']   = $pointID;
		$data['start_date'] = date('Y-m-d');
        $data['ip_address'] = $ip;
		$data['end_date']   = date('Y-01-01', strtotime('+1 year'));
        $this->insert($data);
	}

	public function getOccupedOrFreeIP($type_ip)
	{

		$sql_occuped = "
			select
				OI.*,
				(select u_login from points where point_id = OI.point_id) as u_login
			from occuped_ip as OI
			order by OI.ip_address
		";

		$sql_free = "
			select
				PIA.*,
				(select u_login from points where point_id = PIA.point_id) as u_login
			from point_ip_addresses  as PIA
			where
				PIA.end_date <= now()
			order by PIA.ip_address
		";

		if($type_ip == "free")
		{
			return $this->_db->fetchAll($sql_free);
		}
		else
		{
			return $this->_db->fetchAll($sql_occuped);
		}
	}
}
