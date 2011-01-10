<?php
/**
 * Model of dslam_list table
 *
 * @author marat
 */

require_once('BaseModel.php');

class DslamList extends BaseModel
{
    protected $_name = 'dslam_list';
    protected $_sequence = 'dslam_list_seq';

    const CLIENT_TYPE_MIXED = 2;
    const CLIENT_TYPE_STREAM = 1;
    const CLIENT_TYPE_CORPORATION = 0;

    /**
     * Gets first avalibalse dslam - mean where empty port exists
     *
     * @param integer $atsID - Ats where find dslam
     * @param integer $clientType - Client type (0, 2, 3 - juridical; 1, 4 - physical)
     */
    public function getFirstAvailable($atsID, $clientType)
    {

        $clientType = intval($clientType);

        // If client is juridical
        if ($clientType == 0 || $clientType == 2 || $clientType == 3)
        {
            $clientType = '0';
        }
        elseif ($clientType == 1 || $clientType == 4) // If cliens is physical
        {
            $clientType = 1;
        }

        $sql = "
            SELECT
                dslam_list.id
            FROM
                dslam_list
                JOIN ports ON ports.dslam_id = dslam_list.id
                    AND ports.status = 0
            WHERE
                dslam_list.ats_id = {$atsID}
            AND
				dslam_list.is_deleted = false
        ";

        return $this->_db->fetchOne($sql);
    }

    /**
     * Gets id=>name array for select options
     *
     * @param string $where - filter condition
     */
    public function getOptions($where)
    {
        $dslamList = $this->fetchAll($where, 'name');
        $dslamOptions = array();

        foreach ($dslamList as $dslam)
        {
            $dslamOptions[$dslam->id] = $dslam->name;
        }

        return $dslamOptions;
    }

    public function getOptionsWithIps($where)
    {
        $dslamList = $this->fetchAll($where, 'name');
        $dslamOptions = array();

        foreach ($dslamList as $dslam)
        {
            $dslamOptions[$dslam->id] = $dslam->name . ' (' . $dslam->ip_address . ')';
        }

        return $dslamOptions;
    }

    public function fetchGroupByAts()
    {
    	$dslams = $this->fetchAll("is_deleted = false", 'name')->toArray();

    	$result = array();

    	foreach ($dslams as $dslam)
    	{
    		$result[$dslam['ats_id']][] = $dslam;
    	}

    	return $result;
    }

    public function fetchAllByAts($atsID)
    {
        $atsID = intval($atsID);

        if ( ! $atsID )
        {
            return array();
        }

        return $this->fetchAll("is_deleted = false AND ats_id = {$atsID}", 'name')->toArray();
    }

    public function DslamRebotList($ipaddress)
    {
        // перегрузка дсламов
    	$sql = "
                select
                        PTS.*,
                        PTS.dslam_id as dslamid,
                        PTS.client_id as port_clientid,
                        (select client_type_id from clients where client_id = PTS.client_id limit 1) as client_type_id,
                        (select number from ports where id = PTS.port_id) as port_number,
                        (select speed from client_services where need_cross = 1 and point_id = PTS.point_id) as speed
                from points as PTS
                where
                        PTS.dslam_id = (select id from dslam_list where ip_address = '{$ipaddress}')
        ";

        return $this->_db->fetchAll($sql);


    }

    public function WriteTaskFroReboot($data, $needDslamIP)
    {
    	if($data['speed'] == "резервирование")
    	{
    		$tasktype = 2;
    	}
    	else
    	{
    		$tasktype = 0;
    	}

        $portTasksModel = new Porttasks();
        $atsListModel   = new AtsList();
        // Проверка на 1МБ на АТС

        $data['portspeed'] = $data['speed'];

        if($data['client_type_id'] == 1)
        {
            $expanded = $atsListModel->verifyAts($data['ats_id']);
            if($expanded == "t")
            {
               //$data['portspeed'] = '1024/512';
               $data['portspeed'] = $portTasksModel->getAddServiceSpeed($data['point_id']);
            }
        }

        $data['dslam_ip']  = $needDslamIP;

        $portTasksModel->addTask($data, $tasktype);
    }
}