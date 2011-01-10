<?php

require_once('Zend/Db/Table.php');

class SearchAbonDepartment extends Zend_Db_Table
{
	public function searchWithParamToPaginator($param, $value, $table = null, $flag = 1)
	{
		$select = $this->_db->select()->from('clients AS cla')
			->join('points AS pts', 'pts.client_id = cla.client_id')
			->joinLeft('ats_list', 'ats_list.id = pts.ats_id')
			->joinLeft('ports', 'ports.id = pts.port_id', array('state as status'));

		/*if ($table)
		{
                    $select = $select->join('client_services', "client_services.point_id = pts.point_id", array());
		}
                 */

		$value = trim($value);
		$value = strtolower($value);

		if ($value)
		{
			if ($param == 'CLA.client_id')
			{
				$value = intval($value);
				$select = $select->where("{$param} = {$value}");
			}
			else
			{
				$select = $select->where("LOWER({$param}) LIKE '%{$value}%'");
			}
		}
                $obj = new Zend_Paginator_Adapter_DbSelect($select);

                return $obj;
             
            //return $this->_db->fetchAll('select * from points where pcross = \'2123865\'');
	}
        
        
        public function serchViaIframe($param, $value, $table = null, $flag = 1)
	{
		$select = $this->_db->select()->from('clients AS cla')
			->join('points AS pts', 'pts.client_id = cla.client_id')
			->joinLeft('ats_list', 'ats_list.id = pts.ats_id')
			->joinLeft('ports', 'ports.id = pts.port_id', array('state as status'))
                        ->joinLeft('client_services',  'client_services.point_id = pts.point_id');


		$value = trim($value);
		$value = strtolower($value);

		if ($value)
		{
			if ($param == 'CLA.client_id')
			{
				$value = intval($value);
				$select = $select->where("{$param} = {$value}");
			}
			else
			{
				$select = $select->where("LOWER({$param}) LIKE '%{$value}%'");
			}
		}
                $obj = new Zend_Paginator_Adapter_DbSelect($select);

                return $obj;
             
            //return $this->_db->fetchAll('select * from points where pcross = \'2123865\'');
	}

	public function searchWithParam($param, $value)
	{
		$value = strtolower($value);

		if($value == "" || $param == "CLA.client_id")
		{
			$value = trim($value);

			if($value == "")
			{
				$sql = "
					SELECT
						CLA.*,
						PTS.*,
						ats_list.name AS ats_name,
						(select state from ports where id = PTS.port_id) as status
					FROM 
						clients as CLA 
					JOIN 
						points as PTS ON PTS.client_id = CLA.client_id 
					LEFT JOIN
						ats_list ON ats_list.id = PTS.ats_id
				";
				return $this->_db->fetchAll($sql);

			} 
			else 
			{
				$sql = "
					SELECT
						CLA.*,
						PTS.*,
						ats_list.name AS ats_name,
						(select state from ports where id = PTS.port_id) as status
					FROM 
						clients as CLA 
					JOIN 
						points as PTS ON PTS.client_id = CLA.client_id 
					LEFT JOIN
						ats_list ON ats_list.id = PTS.ats_id
					WHERE
						{$param} = {$value}
				";
				 
				return $this->_db->fetchAll($sql);
			}
			//$sql .= " and PTS.u_login not like '%shd%'";
		}
		else
		{
			$value = trim($value);
			$sql = "
				SELECT
					CLA.*,
					PTS.*,
					ats_list.name AS ats_name,
					(select state from ports where id = PTS.port_id) as status
				FROM 
					clients as CLA 
				JOIN 
					points as PTS ON PTS.client_id = CLA.client_id 
				LEFT JOIN
					ats_list ON ats_list.id = PTS.ats_id
				WHERE
					LOWER({$param}) like LOWER('%{$value}%')
			";
			//$sql .= " and PTS.u_login not like '%shd%'";
		}

		return $this->_db->fetchAll(strtolower($sql));
	}

	public function showOldInfoClient($client_id)
	{
		$sqlclient = "
		select
			CLA.*, CNTR.*
		from clients as CLA, contracts as CNTR
			where
				CLA.client_id = CNTR.client_id
			and
				CLA.client_id = '{$client_id}'
		";
		return $this->_db->fetchRow($sqlclient);
	}

	public function saveNewClientInfo($data)
	{
		$sql = "
			update clients set
				client_name = '{$data['client_name']}',
				legaladdress = '{$data['legaladdress']}',
				address = '{$data['address']}',
				phone = '{$data['phone']}',
				fax = '{$data['fax']}',
				email = '{$data['email']}',
				client_orient = '{$data['client_orient']}'
			where
				client_id = {$data['client_id']}
		";
		return $this->_db->fetchAll($sql);
	}

	public function showOldInfoPoint($client_id, $point_id)
	{
		$sql = "
		select * from points
		where
			client_id = {$client_id}
		and
			point_id = {$point_id}
		";
		return $this->_db->fetchAll($sql);
	}

	public function saveNewPointInfo($newData, $prefix)
	{
		/*
		*  метод апдейтит информацию (после поиска) о точке
		*/
		$sql = "
		update points set
			phone = '{$newData['phone']}',
			pcross = '{$newData['pcross']}',
			country_id = '{$newData['country_id']}',
			connect_address = '{$newData['connect_address']}',
			contact_name = '{$newData['contact_name']}',
			u_login = '{$prefix}'
		where
			point_id = '{$newData['point_id']}'
		and
			client_id = '{$newData['client_id']}'
		";

		return $this->_db->fetchAll($sql);
	}

	public function virualSelect($table, $point_id)
	{
		/*
		*  метод вытаскивает информацию об услугах на указанной точке point_id
		*/
		$sql = "
		select
			LINK.*,

			(select client_type_id from points, clients
			where points.client_id = clients.client_id
			and
				points.point_id = LINK.point_id) as client_type_id,

			(select servicetype_name from
			service_type as AST, tarifs as TAR
			where
				AST.servicetype_id = TAR.servicetype_id
				and
				TAR.tarif_id = LINK.tarif_id
				) as servicetype_name,

			(select AST.servicetype_id from
			service_type as AST, tarifs as TAR
			where
				AST.servicetype_id = TAR.servicetype_id
				and
				TAR.tarif_id = LINK.tarif_id
				) as servicetype_id


		from {$table} as LINK
			where
				point_id = {$point_id}
			and
				penable = true

		";
		return $this->_db->fetchAll($sql);
	}

	public function updateServiceAfterSearch($data,$table, $newid)
	{
		/*
		*  метод апдейтит строки из таблиц услуг
		*/
		$sql = "
		update {$table} set
			contact_name = '{$data['contact_name']}',
			contact_phone = '{$data['phone']}',
			service_address = '{$data['connect_address']}'

		where
			{$table}.id = {$newid}
		";

		return $this->_db->fetchAll($sql);
	}

	public function deleteServiceAfterSearch($tablename, $service_id, $point_id)
	{
		$sqlServices = "
			delete from {$tablename}
			where
				{$tablename}.id = $service_id
			and
				point_id = {$point_id}
		";

		$this->_db->fetchAll($sqlServices);
	}
}
?>
