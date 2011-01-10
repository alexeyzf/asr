<?php
require_once('Zend/Db/Table.php');

class DebtorModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';


	public function getDebtorList($type = null)
	{
		$select = $this->_db->select()->from('clients')
						->join('points', 
							'points.client_id = clients.client_id AND points.statuscross >= -1', 
							array())
						->where('clients.is_employee = false')
						->where('clients.is_donate = false')
						->where('clients.vip = false');
		
		if ($type == 2)
		{
			$select = $select->where('clients.client_type_id = 1')
							->where("points.u_login like 'shssm%'")
							->where('clients.ballance <= -0.01');
		}
		elseif ($type == 1)
		{
			$select = $select->where('clients.client_type_id = 1')
							->where("points.u_login like 'shs%'")
							->where('clients.ballance <= -0.01');
		}
		else 
		{
			$select = $select->where('clients.client_type_id = 0')
							->where('clients.ballance < -5');
		}
		
		$select = $select->distinct()->order('clients.ballance');
		
		return $this->_db->fetchAll($select);
	}

	public function getServiceDebtors($tablename)
	{
		$sql = "
			select
				CLA.client_id,
				CLA.client_name,
				CLA.ballance,
				PTS.u_login,
				PTS.phone
			from {$tablename} as H, points as PTS, clients as CLA
			where
				CLA.client_id = PTS.client_id
			and
				PTS.point_id = H.point_id
			and
				CLA.ballance <= -0.01
		";
		return $this->_db->fetchAll($sql);
	}

	public function getAllPointsInBlackList()
	{
		$sql = "
			SELECT
				points.client_id,
				clients.client_type_id,
				clients.ballance,
				clients.client_name,
				clients.is_employee,
				clients.is_donate,
				clients.vip,
				clients.ballance_change,
				points.point_id,
				points.pcross,
				points.notes,
				dslam_list.ip_address AS dslam_ip,
				ports.number AS portnumber,
				0 AS speed
			FROM
				points
			JOIN
				clients ON clients.client_id = points.client_id
			LEFT JOIN
				dslam_list on dslam_list.id = points.dslam_id
			LEFT JOIN
				ports ON ports.id = points.port_id
			WHERE
				clients.client_type_id = 0
			AND
				points.statuscross = -1
			AND
				clients.ballance <= -0.01
			AND
				clients.ballance_change + INTERVAL '45 day' <= now()
			AND
				dslam_list.ip_address is not null
			order by clients.client_id
		";

		return $this->_db->fetchAll($sql);
	}

}

?>
