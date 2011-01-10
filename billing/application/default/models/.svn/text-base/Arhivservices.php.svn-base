<?php
require_once('Zend/Db/Table.php');

class Arhivservices extends Zend_Db_Table
{
    protected $_name = 'clients_arhiv';

    public function getArhivService($client_id, $point_id, $tablename)
    {
    	$sql = "
		select
			CLA.*,
			PTS.*,
			LINK.*,
			PTS.u_login as login,

			(select tarif_name from tarifs where
			tarif_id = LINK.tarif_id ) as tarif_name,

			(select tablelink from tarifs where
			tarif_id = LINK.tarif_id ) as tablelink,

			(select servicetype_name from service_type as ST, tarifs as TAR
			where
			ST.servicetype_id = TAR.servicetype_id
			and
			TAR.tarif_id = LINK.tarif_id) as servicetype_name

		from {$tablename} as LINK, clients as CLA, points as PTS
		where
			CLA.client_id = {$client_id}
		and
			PTS.point_id  = {$point_id}
		and
			LINK.point_id = {$point_id}
		and
			LINK.is_deleted = true
		";

		return $this->_db->fetchAll($sql);
    }

	public function setOnOffService($tablename, $point_id, $tarif_id, $service_id, $client_id, $flag)
	{
		/**
		 *  1 - false
		 *  0 - true
		 */

		$sqlNeedCross = "
			select need_cross from service_type as ST, tarifs as TAR
				where
					ST.servicetype_id = TAR.servicetype_id
				and
					TAR.tarif_id = {$tarif_id}
		";
		$need_cross = $this->_db->fetchOne($sqlNeedCross);


		if($flag == 'true')
		{
			$setFlagSql = "
			update
				{$tablename}
			set
				is_deleted = true,
				penable = false,
				enddate = current_date
			where
				id = {$service_id}
			and
				point_id = {$point_id}
			and
				tarif_id = {$tarif_id}
			and
				current_date between startdate and enddate
			and
				enddate > current_date
			";
			$this->_db->fetchAll($setFlagSql);
		}
		else
		{
			$updateToFalse = "
			update {$tablename} set is_deleted = false
			where
				id = {$service_id}
			and
				point_id = {$point_id}
			and
				tarif_id = {$tarif_id}
			and
				current_date between startdate and enddate
			and
				enddate > current_date
			";
			$this->_db->fetchAll($updateToFalse);
		}

	}

	public function dropClientInArhiv($client_id)
	{
		$login_sql = "
				  delete from radcheck where username = (
				  select u_login from clients as CLA, points as PTS
					where
				  CLA.client_id = PTS.client_id
					and
				  PTS.client_id = {$client_id} limit 1
				  )
				 ";

		$u_login = $this->_db->fetchAll($login_sql);


		$sqlcontract = "
			delete from points where client_id = {$client_id}
		";
		$this->_db->fetchAll($sqlcontract);

		$sqlclient = "
			delete from clients where client_id = {$client_id}
		";
		$this->_db->fetchAll($sqlclient);
	}

	public function quantityOfServices($client_id)
	{
		$sqlverify = "
		select LINKVIEW.client_id
			from ballance_in_passiv_and_service_off as LINKVIEW
		where client_id = {$client_id}
		";
		return $this->_db->fetchOne($sqlverify);
	}

	public function searchInArhiv($type, $word)
	{
		if($type == "" || $word == "")
		{
			$sql = "
			select
				CLA_a.*,
				PTS_a.*
			from clients_arhiv as CLA_a, points_arhiv as PTS_a
			where
				CLA_a.client_id = PTS_a.client_id
			";
			return $this->_db->fetchAll($sql);
		}

		if($type == "CLA_a.client_id" || $type == "PTS_a.point_id")
		{
			$sql = "
			select
				CLA_a.*,
				PTS_a.*
			from clients_arhiv as CLA_a, points_arhiv as PTS_a
			where
				CLA_a.client_id = PTS_a.client_id
			and
				{$type} = {$word}
			";
			return $this->_db->fetchAll($sql);
		}
		else
		{
			$sql = "
			select
				CLA_a.*,
				PTS_a.*
			from clients_arhiv as CLA_a, points_arhiv as PTS_a
			where
				CLA_a.client_id = PTS_a.client_id
			and
				{$type} ilike '%{$word}%'
			";
			return $this->_db->fetchAll($sql);
		}
	}


	public function restoreClientAndAllPoints($client_id, $point_id)
	{
		$clientNameSQL = "
		select client_name from clients_arhiv where client_id = $client_id
		";
		$c_name = $this->_db->fetchOne($clientNameSQL);

		$sqlClient = "
		delete from clients_arhiv as CLA_a where CLA_a.client_id = {$client_id}
		";
		$this->_db->fetchAll($sqlClient);

		$sqlPoints = "
		delete from points_arhiv as PTS_a where PTS_a.client_id = {$client_id}
		";
		$this->_db->fetchAll($sqlPoints);
		return $c_name;
	}

	public function setStatuscrossUncross($point_id)
	{
		$sql = "
			update points set statuscross = -1, status_before = statuscross
			where
				point_id = {$point_id}
		";
		$this->_db->fetchAll($sql);
	}
}
?>
