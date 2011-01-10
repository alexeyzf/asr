<?php
/**
 * Model for free_traffic table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class FreeTraffic extends Zend_Db_Table
{
	protected $_name = 'free_traffic';

	public function getTraffic($login, $startDate)
	{
		$endDate = date('Y-m-t', strtotime($startDate));

		$sql = "
			SELECT
				sum(traffic_out)::real / 1024 / 1024 as input,
				sum(traffic_in)::real / 1024 / 1024 as output
			FROM
				 free_traffic
			WHERE
				username = '{$login}'
				AND date >= '{$startDate}'
				AND date <= '{$endDate}'
		";

		return $this->_db->fetchRow($sql);
	}

	public function getTrafficByClientID($clientID, $startDate)
	{
		$endDate = date('Y-m-t', strtotime($startDate));

		$sql = "
			SELECT
				sum(traffic_out)::real / 1024 / 1024 as input,
				sum(traffic_in)::real / 1024 / 1024 as output
			FROM
				 free_traffic
			WHERE
				username = (SELECT u_login FROM points where client_id = {$clientID} limit 1)
				AND date >= '{$startDate}'
				AND date <= '{$endDate}'
		";

		return $this->_db->fetchRow($sql);
	}

	public function insertForTrafficDetails($data)
	{
		$sql = "
			insert into traffic_garbage (starttime, endtime, ip_address)
			values
			(
				'{$data['start']}',
				'{$data['end']}',
				'{$data['ip_address']}'
			)
		";
		$this->_db->fetchAll($sql);
	}

	public function searchDetailsFromDB($ip)
	{
		$sql = "
			select * from traffic_garbage_collector
			where
				(ip_sender = '{$ip}' or ip_to = '{$ip}')
		";
		return $this->_db->fetchAll($sql);
	}
}