<?php
/**
 * Model for client_services view
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class ClientServicesModel extends Zend_Db_Table
{
	protected $_name = 'client_services';
	protected $_primary = array('point_id', 'service_id');

	public function getServices($clientTypeID = null, $needCross = null)
	{
		$select = $this->_db->select()->from($this->_name)
			->join('ports', "ports.id = {$this->_name}.port_id", array('state AS port_state'));

		if ( isset($clientTypeID) )
		{
			$select = $select->where('client_type_id  = ?', $clientTypeID);
		}

		if ( isset($needCross) )
		{
			$select = $select->where('need_cross =  ?', $needCross);
		}

		$select = $select->order('client_name');

		return $select->query()->fetchAll();
	}

	public function getService($where)
	{
		$row = $this->fetchRow($where);

		if ( $row )
		{
			$result = $row->toArray();

			if ($result['tablename'] == 'collacation'
				|| $result['tablename'] == 'tradtel'
				|| $result['tablename'] == 'isdn')
			{
				$abonPriceSql = "
					SELECT
						abon_price
					FROM
						{$result['tablename']}
					WHERE
						id = {$result['service_id']}
				";

				$result['abon_price'] = $this->_db->fetchOne($abonPriceSql);
			}

			return $result;
		}
		else
		{
			return null;
		}
	}

	public function getPointService($pointID)
	{
		$whereSql = "point_id = {$pointID}";
		return $this->getService($whereSql);
	}


	public function getNotActiveService($pointID, $tableName, $serviceID)
	{
		$whereSql = "
			point_id = {$pointID}
			AND tablename = '{$tableName}'
			AND is_forced = false
		";

		if ($serviceID)
		{
			$whereSql .= " AND service_id = {$serviceID} ";
		}

		return $this->getService($whereSql);
	}
	
	public function getServiceByID($pointID, $tableName, $serviceID)
	{
		$whereSql = "
			point_id = {$pointID}
			AND tablename = '{$tableName}'
		";

		if ($serviceID)
		{
			$whereSql .= " AND service_id = {$serviceID} ";
		}

		return $this->getService($whereSql);
	} 

	public function getActiveService($pointID, $tableName, $serviceID, $excludeID = null)
	{
		$whereSql = "
			point_id = {$pointID}
			AND tablename = '{$tableName}'
			AND is_forced = true
		";

		if ($serviceID)
		{
			$whereSql .= " AND service_id = {$serviceID} ";
		}
		
		if ($excludeID)
		{
			$whereSql .= " AND service_id != {$excludeID} ";
		}

		return $this->getService($whereSql);
	}

	public function enableService($tableName, $ID, $startDate)
	{
		$paidto = date('Y-m-01', strtotime('+1 month'));

		$sql = "
			UPDATE
				{$tableName}
			SET
				paidto = '{$paidto}',
				penable = true,
				is_forced = true,
				startdate = '{$startDate}'
			WHERE
				id = {$ID}
		";

		$this->_db->fetchOne($sql);
	}

	public function disableService($tableName, $ID, $endDate)
	{
		$sql = "
			UPDATE
				{$tableName}
			SET
				penable = false,
				enddate = '{$endDate}'
			WHERE
				id = {$ID}
		";

		$this->_db->fetchOne($sql);
	}

	public function saveDates($tableName, $ID, $startDate, $endDate, $isForced, $newTarifID)
	{
		$data['startdate'] = date('Y-m-d', strtotime($startDate));
		$data['enddate'] = date('Y-m-d', strtotime($endDate));
		$data['tarif_id'] = $newTarifID;

		if ($isForced)
		{
			$data['is_forced'] = new Zend_Db_Expr('true');
		}
		else
		{
			$data['is_forced'] = new Zend_Db_Expr('false');
		}

		$this->_db->update($tableName, $data, "id = {$ID}");
	}
	
	public function getPointServices($pointID, $startDate, $endDate)
	{
		$serviceTypeModel = new ServiceType();
		$tables = $serviceTypeModel->getServicesTables();
		
		foreach ($tables as $tableName)
		{
			$stables[$tableName] = $tableName;
		}
		
		$services = array();
		
		foreach ($stables as $tableName)
		{
			if ( ! $tableName
    			|| $tableName == 'ivr'
    			|| $tableName == 'tel'
    			|| $tableName == 'lvs') // ivr, tel, lvs does not exist now
    		{
    			continue;
    		}
			
    		$sql = $this->_db->select()
    				->from(array('s' => $tableName), array('id', 'startdate', 'enddate'))
    				->join(array('t' => 'tarifs'), 't.tarif_id = s.tarif_id', array('servicetype_id', 'tarif_id', 'tarif_price'))
    				->join(array('p' => 'points'), 'p.point_id = s.point_id', array())
    				->join(array('c' => 'clients'), 'c.client_id = p.client_id', array('client_type_id'))
    				->where('s.point_id = ?', $pointID)
    				->where('s.startdate <= ?', $endDate)
    				->where('s.enddate > ?', $startDate)
    				->where('s.is_deleted = false')
    				->where('(s.is_forced = true AND c.client_type_id = 0) OR (s.paidto >= \'' . $startDate . '\' AND c.client_type_id = 1)');
    		$test = $this->_db->fetchAll($sql);
    		
    		foreach ($test as $item)
    		{
    			$services[] = $item;
    		}
		}
		
		return $services;
	}
}