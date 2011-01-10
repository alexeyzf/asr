<?php
require_once('Zend/Db/Table.php');

class DocumentModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

   	public function getData($pointID)
   	{
   		$sql = "
			select
				PTS.*,
				ATS.*,
				PHL.*,
				PRT.*,
				ATS.name as ats_name
			from points as PTS, ats_list as ATS, phone_hub_list as PHL, ports as PRT
			where
				PTS.point_id = {$pointID}
			and
				PTS.ats_id = ATS.id
			and
				ATS.phone_hub_id = PHL.id
			and
				PTS.port_id = PRT.id limit 1
		";
		return $this->_db->fetchAll($sql);
   	}
}

?>
