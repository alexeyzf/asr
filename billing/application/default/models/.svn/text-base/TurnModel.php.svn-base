<?php
require_once('Zend/Db/Table.php');

class TurnModel extends Zend_Db_Table
{
	protected $_name = 'turns';
	protected $_sequence = 'turns_id_seq';


	public function getTurnList()
	{
		$sql = "
			select
				AL.*,
				T.*,
				T.id as turn_id,
				(select name from phone_hub_list where id = AL.phone_hub_id) as hub_name
			from turns as T, ats_list as AL
			where
				T.ats_id = AL.id
			and
				T.is_deleted = false
			order by AL.phone_hub_id
		";
		return $this->_db->fetchAll($sql);
	}

	public function getEmptyPorts()
	{
		$sql = "
			select count(AL.id), AL.id as ats_id from ports as P, dslam_list as DL, ats_list as AL
			where
				P.status = 0
			and
				P.dslam_id = DL.id
			and
				DL.ats_id = AL.id
			group by AL.id
		";
		return $this->_db->fetchAll($sql);
	}


	public function insertOnTurn($atsID, $pcross, $contact_name, $contact_phone, $userID)
	{
		if($atsID)
		{
			$sql = "
				insert into turns(userid, ats_id, pcross, contact_name, contact_phone)
				values
				({$userID}, {$atsID}, '{$pcross}', '{$contact_name}', '{$contact_phone}')
			";
			$this->_db->fetchAll($sql);
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function markDelete($ID)
	{
		$data['is_deleted'] = "true";

		if($ID)
		{
			$this->update($data, "id = {$ID}");
			return 1;
		}
		else
		{
			return 0;
		}
	}

}