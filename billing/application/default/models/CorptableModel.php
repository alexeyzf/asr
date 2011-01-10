<?php
require_once('Zend/Db/Table.php');

class CorptableModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    public function getNewCorps()
    {
		$sql = "
			select * from
			(select
				CLA.*,
				PTS.*,
				ADSL.*,
				(select servicetype_id from tarifs where tarif_id = ADSL.tarif_id) as stype
			from clients as CLA, points as PTS, adsl as ADSL
			where
				CLA.client_dateagree >= '2009-11-26'
			and
				CLA.client_type_id = 0
			and
				CLA.client_id = PTS.client_id
			and
				PTS.point_id = ADSL.point_id
				) as XXX
			where
				XXX.stype = 7000
		";
		return $this->_db->fetchAll($sql);
    }
}

?>
