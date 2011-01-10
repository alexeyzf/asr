<?php
require_once('Zend/Db/Table.php');

class PortReportModel extends Zend_Db_Table
{

    public function RetAts()
    {
        $sql = "
            SELECT * FROM ats_list ORDER BY name
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getHubIDs()
    {
        $sql = "
            select * from phone_hub_list order by id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getAtsByHub($hub_id)
    {
        $sql = "
            select * from ats_list where phone_hub_id = {$hub_id} order by name
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getDslamNamesByAtsID($ats_id)
    {
        $sql = "
            SELECT
                DL.*,
                (select a_value from asrtypes where typename_value = '5' and typename_id = DL.type_id) as countport
            FROM dslam_list as DL
            WHERE
				ats_id = {$ats_id}
			and
				is_deleted = false
			ORDER BY name
        ";
        return $this->_db->fetchAll($sql);
    }


    public function getStatus($dslamID)
    {
        $sql = "
                SELECT
            count(*) as countp,
            (select state from ports where id = PTS.port_id)  as portstatus
            FROM clients as CLA, points as PTS
            WHERE
                    CLA.client_id = PTS.client_id
            and
                    PTS.dslam_id = {$dslamID}
            group by portstatus
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getStateBroken($dslamID)
    {
        $sql = "
            select
                    count(*) as countb
            from ports
            where
                    status in (-1, -5) and dslam_id = {$dslamID}
        ";
        return $this->_db->fetchAll($sql);
    }
}