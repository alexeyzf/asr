<?php
require_once('BaseModel.php');

class DemandModel extends BaseModel
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    public function getDataSID($point_id)
    {
		$sql = "
			select
				A.tarif_id,
				A.discount,
				(select tarif_price from tarifs where tarif_id = A.tarif_id) as tarif_price,
				(select group_name from tarifs where tarif_id = A.tarif_id) as group_name,
				(select servicetype_id from tarifs where tarif_id = A.tarif_id) as servicetype_id,
				A.startdate,
				A.enddate,
				A.paidto,
				A.tablename,
				A.id as service_id,
				PTS.client_id,
				A.admin_id,
				PTS.u_login
			from adsl as A, points as PTS
			where
				PTS.point_id = {$point_id}
			and
				PTS.point_id = A.point_id
			and current_date >= startdate
			UNION

			select
				A.tarif_id,
				A.discount,
				(select tarif_price from tarifs where tarif_id = A.tarif_id) as tarif_price,
				(select group_name from tarifs where tarif_id = A.tarif_id) as group_name,
				(select servicetype_id from tarifs where tarif_id = A.tarif_id) as servicetype_id,
				A.startdate,
				A.enddate,
				A.paidto,
				A.tablename,
				A.id as service_id,
				PTS.client_id,
				A.admin_id,
				PTS.u_login
			from tasix as A, points as PTS
			where
				PTS.point_id = {$point_id}
			and
				PTS.point_id = A.point_id
			and current_date >= startdate
			
			UNION

			select
				A.tarif_id,
				A.discount,
				(select tarif_price from tarifs where tarif_id = A.tarif_id) as tarif_price,
				(select group_name from tarifs where tarif_id = A.tarif_id) as group_name,
				(select servicetype_id from tarifs where tarif_id = A.tarif_id) as servicetype_id,
				A.startdate,
				A.enddate,
				A.paidto,
				A.tablename,
				A.id as service_id,
				PTS.client_id,
				A.admin_id,
				PTS.u_login
			from wifi as A, points as PTS
			where
				PTS.point_id = {$point_id}
			and
				PTS.point_id = A.point_id
			and current_date >= startdate
			
			UNION

			select
				A.tarif_id,
				A.discount,
				(select tarif_price from tarifs where tarif_id = A.tarif_id) as tarif_price,
				(select group_name from tarifs where tarif_id = A.tarif_id) as group_name,
				(select servicetype_id from tarifs where tarif_id = A.tarif_id) as servicetype_id,
				A.startdate,
				A.enddate,
				A.paidto,
				A.tablename,
				A.id as service_id,
				PTS.client_id,
				A.admin_id,
				PTS.u_login
			from vpn as A, points as PTS
			where
				PTS.point_id = {$point_id}
			and
				PTS.point_id = A.point_id
			and current_date >= startdate
			order by startdate desc
		";
		return $this->_db->fetchRow($sql);
    }


    public function starttest($data, $point_id)
    {
    	$start = date('Y-m-d');
    	$end   = date('Y-01-01', strtotime("+1 year"));

		if(!$data['discount'])
		{
			$data['discount'] = 0;
		}

		if($data['service_id'])
		{
			//
			$sql = "
				insert into {$data['tablename']}
					(	point_id,
						startdate,
						enddate,
						admin_id,
						tarif_id,
						discount,
						tablename,
						\"login\",
						is_forced,
						auto_start
					)
						values
					(
						{$point_id},
						'{$start}',
						'{$end}',
						{$data['admin_id']},
						{$data['tarif_id']},
						{$data['discount']},
						'{$data['tablename']}',
						'{$data['u_login']}',
						false,
						false
					)
			";
			$this->_db->fetchAll($sql);
			
			return $this->_db->lastInsertId($data['tablename']);
		}
    }

}
?>