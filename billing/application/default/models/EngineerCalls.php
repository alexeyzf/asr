<?php
/**
 * Model of engineer_calls table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class EngineerCalls extends Zend_Db_Table
{
    protected $_name = 'engineer_calls';
    protected $_sequence = 'engineer_calls_seq';

    public function addCall($pointID)
    {
        $data['point_id'] = $pointID;
        $data['status'] = 0;
        $this->insert($data);
    }

    public function getCallsList($point_id = "")
    {
        $sql = "
        select
			distinct CLA.client_id,
            EC.*,
            PTS.*,
            CLA.*,
            ATS.*,
            DSLAM.*,
            PRT.*,
            DSLAM.name as dslam_name,
            CNTR.*,
            EC.status as statuscall,
            ADSL.*,
            PTS.contact_name as service_contact_name,
            (select speed from tarifs where tarif_id = ADSL.tarif_id) as speed,
            PTS.u_login
        from engineer_calls as EC
            left join
            points as PTS on(EC.point_id = PTS.point_id)
            left join
            ports as PRT on(PTS.port_id = PRT.id)
            left join
            clients as CLA on(PTS.client_id = CLA.client_id)
            left join
            adsl as ADSL on(PTS.point_id = ADSL.point_id)
            left join
            ats_list as ATS on(PTS.ats_id = ATS.id)
            left join
            dslam_list as DSLAM on(PTS.dslam_id = DSLAM.id)
            left join
            contracts as CNTR on(CLA.client_id = CNTR.client_id)
        where
            CLA.client_type_id = 0
        and
            CNTR.contract_type_id = 1
        and
            EC.status <> 2
        and
            ADSL.point_id is not null
        ";

        if($point_id)
        {
            $sql .= " and EC.point_id = {$point_id} order by PTS.pcross";
        }
        else
        {
            $sql .= " order by PTS.pcross";
        }

        return $this->_db->fetchAll($sql);
    }


    public function getStatusCall()
    {
        $sql = "select * from engineer_calls_status_view";
        $arr = $this->_db->fetchAll($sql);

        foreach($arr as $key => $value)
        {
            $param[$value['typename_id']] = $value['typename'];

        }
        return $param;
    }

    public function updateStatusCall($point_id, $status, $modem)
    {
        $sql = "
        update engineer_calls set
                status = {$status},
                modem_serial = '{$modem}',
                last_change  = 'now()'
        where
                point_id = {$point_id}
        ";

        return $this->_db->fetchAll($sql);
    }

    public function startTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_db->commit();
    }

    public function rollbackTransaction()
    {
        $this->_db->rollBack();
    }
}