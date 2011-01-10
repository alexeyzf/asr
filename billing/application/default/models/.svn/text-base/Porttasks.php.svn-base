<?php
/**
 * Description of Porttasks
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');
require_once('Ports.php');
require_once('EditPointModel.php');
require_once('ClientModel.php');
require_once('AtsList.php');
require_once('PointIpAddresses.php');

class Porttasks extends Zend_Db_Table
{
    protected $_name = 'porttasks';
    protected $_sequence = 'porttasks_seq';

    const TASK_TYPE_ON = 0;
    const TASK_TYPE_CHANGE = 1;
    const TASK_TYPE_OFF = 2;

    /**
    * Add Task to port task table, which used by cron
    *
    * @param array $pointInfo
    * @param integer $taskType
    */
    public function addTask($pointInfo, $taskType)
    {
        if ( ! $pointInfo ||  ! $pointInfo['dslam_id'])
        {
            return;
        }

		$clientModel = new ClientModel();
		$crossService = $clientModel->getCrossServiceForPoint($pointInfo['point_id'], 1);

		if ($crossService['vlan'])
		{
			$pointInfo['vlan'] = $crossService['vlan'];
		}

        $auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();

        //  Далее следует получать IP адреса в строчку и т.д.
        $pointIPAdressModel = new PointIpAddresses();
        $ips = $pointIPAdressModel->getPointIpAddresses($pointInfo['point_id']);

        if ($ips && is_array($ips))
        {
        	$pointInfo['ip_address'] = implode(' ', $ips);
        }

        $data['tasktype']      = $taskType;
        $data['dslamid']       = $pointInfo['dslam_id'];
        $data['dslamip']       = $pointInfo['dslam_ip'];
        $data['portnumber']    = $pointInfo['port_number'];
        $data['speed']         = $pointInfo['portspeed'];
        $data['port_clientid'] = $pointInfo['client_id'];

        if($crossService['servicetype_id'] == 3000 and $crossService['client_type_id'] == 0)
        {
			$data['ctype']         = 1;
        }
        else
        {
        	$data['ctype']         = $pointInfo['client_type_id'];
        }
        $data['ipaddress']     = $pointInfo['ip_address'];
        $data['vlan']          = $pointInfo['vlan'];
        $data['managerid']     = $manager->id;
        $data['datecreate']    = 'now()';
        $data['startdate']     = 'now()';

		if($data['speed'] == "резервирование")
		{
			$data['tasktype'] = 2;
		}

		// Проверка АТС на оптику
		$atsModel = new AtsList();
		$flag 	  = $atsModel->verifyAts($crossService['ats_id']);

		// Addon for Dynamc Unlim
		// Если тариф из серии special и его АТС не входит в кольцо ВОК то делаем так
		if($crossService['group_name'] == 'special' and !$flag)
		{
				$dynamicModel = new DynamicUnlimModel();
				$dynamicData  = $dynamicModel->getSpeedSeparate($crossService['service_id']);

				if($dynamicData['speed_up'] <= 100)
				{
					$dynamicSpeed = '128/128';
				}
				elseif($dynamicData['speed_up'] <= 200)
				{
					$dynamicSpeed = '256/256';
				}
				elseif($dynamicData['speed_up'] <= 400)
				{
					$dynamicSpeed = '512/512';
				}
				else
				{
					$dynamicSpeed = '768/768';
				}

				$data['speed'] = $dynamicSpeed;
		}
		// End addon for Dynamc Unlim

        $this->insert($data);

        $portModel = new Ports();
        if ($taskType == self::TASK_TYPE_OFF)
        {
            $portData['state'] = 0;
        }
        else
        {
            $portData['state'] = 1;
        }
        $portModel->update($portData, "id = {$pointInfo['port_id']}");
    }


    public function addPointsTasks($pointIDs, $taskType)
    {
        $pointModel = new EditPointModel();

        $points = $pointModel->getPointsInfo($pointIDs);

        foreach ($points as $point)
        {
        	if ($taskType != 2 && $point['statuscross'] == 25)
        	{
            	$this->addTask($point, $taskType);
        	}
        	elseif ($taskType == 2)
        	{
        		$this->addTask($point, $taskType);
        	}
        }
    }

    public function getAll()
    {
    	$db = $this->getAdapter();
    	$sql = "
    		SELECT pt.*, p.u_login, p.pcross, d.name AS dslam_name, ats.name AS ats_name, u.last_name as manager
    		FROM porttasks pt
    		INNER JOIN points p ON p.client_id = pt.port_clientid
    		INNER JOIN dslam_list d ON d.id = pt.dslamid
    		INNER JOIN ats_list ats ON ats.id = d.ats_id
    		LEFT JOIN users u ON u.id = pt.managerid
    		WHERE pt.pdone = false

    	";

    	return $db->fetchAll($sql);
    }


    public function getForPortTasksData($pid, $tablename)
    {
    	$atsModel = new AtsList();

    	$sql = "
		SELECT
			PTS.*,
			PTS.dslam_id as dslamid,
			PTS.client_id as port_clientid,
			(select ip_address from dslam_list where id = PTS.dslam_id) as  dslamip,
			(select number from ports where id = PTS.port_id) as portnumber,
			(select ip_address from network_params where point_id = PTS.point_id limit 1) as ip_address,
			LINK.*,
			(select speed from tarifs where tarif_id = LINK.tarif_id) as speed,
			(select group_name from tarifs where tarif_id = LINK.tarif_id) as group_name,
			(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype,
			(select vlan from network_params where point_id = PTS.point_id) as vlan
	    FROM
	    	points AS PTS,
    		{$tablename} AS LINK
		WHERE
				PTS.point_id = LINK.point_id
			AND
				now() BETWEEN LINK.startdate AND LINK.enddate
			AND
				PTS.point_id = {$pid}
		";

		$pointData = $this->_db->fetchRow($sql);


		$pointIPAddressModel = new PointIpAddresses();
		$ips = $pointIPAddressModel->getPointIpAddresses($pointData['point_id']);

		if ($ips && is_array($ips))
		{
			$pointData['ip_address'] = implode(' ', $ips);
		}

        // тут для стримов. Если атс на 1мб
        $is_expanded = $atsModel->verifyAts($pointData['ats_id']);
        if($is_expanded == "t" and $pointData['ctype'] != 0)
        {
            $speed_profile = "1024/512";
        }
        else
        {
			if($pointData['group_name'] == 'special')
			{
				$dynamicModel = new DynamicUnlimModel();
				$dynamicData  = $dynamicModel->getSpeedSeparate($pointData['id']);

				if($dynamicData['speed_up'] <= 100)
				{
					$dynamicSpeed = '128/128';
				}
				elseif($dynamicData['speed_up'] <= 200)
				{
					$dynamicSpeed = '256/256';
				}
				elseif($dynamicData['speed_up'] <= 400)
				{
					$dynamicSpeed = '512/512';
				}
				else
				{
					$dynamicSpeed = '768/768';
				}

				$pointData['speed'] = $dynamicSpeed;
			}
            $speed_profile = $pointData['speed'];
        }
		$pointData['speed'] = $speed_profile;
		$pointData['tablename'] = $tablename;

        // это тож старость
		if($pointData['group_name'] == "corp_stream")
		{
			$pointData['ctype'] = 1;
		}

		return $pointData;
    }

    public function insertTask($data)
    {
    	$auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();
        $user = $manager->id;

    	$add_sql_param = " ";
    	$add_sql_value = " ";

    	if($data['vlan'] != "")
    	{
    		$add_sql_param = " vlan,";
    		$add_sql_value = " '{$data['vlan']}',";
    	}

    	if ($data['startdate'])
    	{
    		$add_sql_param .= " startdate,";
    		$add_sql_value .= " '{$data['startdate']}',";
    	}

		if($data['speed'] == "резервирование")
		{
			 $data['task_type'] = 2;
		}

    	$sql = "
		insert into porttasks (
							tasktype,
							dslamip,
							portnumber,
							speed,
							ctype,
							dslamid,
							ipaddress,
							{$add_sql_param}
							port_clientid,
							managerid
			)
			values
			(
			 {$data['task_type']},
			 '{$data['dslamip']}',
			 {$data['portnumber']},
			 '{$data['speed']}',
			 {$data['ctype']},
			 {$data['dslamid']},
			 '{$data['ip_address']}',
			 {$add_sql_value}
			 {$data['port_clientid']},
			 {$user}
			)
		";

	$this->_db->fetchAll($sql);
    }


    public function taskList($start, $end, $clientID = null)
    {
		$sql = "
			select
				PRT.*,
				(select first_name || ' ' || last_name from users where id = PRT.managerid) as fullname,
				(select u_login from points where client_id = PRT.port_clientid  and dslam_id = PRT.dslamid limit 1) as u_login
	        from porttasks as PRT
	        where
	        	PRT.datecreate between '{$start}' and '{$end}'
		";

    	if(!$clientID)
    	{
			$sql .= " order by PRT.datecreate desc";
    	}
    	else
    	{
    		$sql .= " and PRT.port_clientid = {$clientID} order by PRT.datecreate desc";
    	}

		return $this->_db->fetchAll($sql);
    }

    public function deleteTasks($idsArray)
    {

        $ids = implode(' ,', $idsArray);


    	$sql = "
			delete from porttasks where id in ({$ids})
	";
		return $this->_db->fetchAll($sql);
    }

    public function getAddServiceSpeed($pointID)
    {
       $sql = "
          select
             T.speed
         from stream_additional_services as SS, tarifs as T
         where
             SS.point_id  = {$pointID}
         and
             now() between SS.startdate and SS.enddate
         and
             SS.tarif_id = T.tarif_id
       ";

       $result = $this->_db->fetchOne($sql);
       if($result)
       {
          return $result;
       }
       else
       {
          return "1024/512";
       }
    }

    public function getNotCarriedTasksData()
    {
    	$sql = "
			select
				P.*,
				(select u_login from points where client_id = P.port_clientid limit 1) as u_login
			from porttasks as P
			where
				P.pdone = false
			order by P.startdate
		";
		return $this->_db->fetchAll($sql);
    }

    public function getSpeedByTarifID($tarifID)
    {
    	$sql = "
			select speed from tarifs where tarif_id = {$tarifID}
		";
		return $this->_db->fetchOne($sql);
    }

}