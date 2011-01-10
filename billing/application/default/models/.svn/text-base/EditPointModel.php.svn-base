<?php
/*
 * Модель нужна для контроллера EditpointController.php
 * Будет реализовывать изменения
 */

 require_once('Zend/Db/Table.php');

class EditPointModel extends Zend_Db_Table
{
    protected $_name = 'points';
    protected $_sequence = 'points_seq';


    public function getFirstCrossService($pointID)
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return array();
        }

        $sql = "
        select
	        SAS.*,
			(select crossdate from points where point_id = {$pointID} limit 1) as crossdate,
	        (select short_name from service_type where  servicetype_id = SAS.servicetype_id) as short_name
        FROM client_services as SAS
        WHERE
            SAS.point_id = {$pointID}
        AND
			SAS.servicetype_id <> 9999
		AND
            SAS.need_cross > 0
        ";

        $data = $this->_db->fetchRow($sql);

        if ( ! $data['tablename'] )
        {
            return array();
        }

        $statusSql = "
            SELECT
                penable
            FROM
                {$data['tablename']}
            WHERE
                {$data['tablename']}.id = {$data['service_id']}
        ";

        $data['status'] = $this->_db->fetchOne($statusSql);

        return $data;
    }

    public function getClientID($pointID)
    {
    	$sql = "
    		SELECT
    			client_id
    		FROM
    			points
    		WHERE
    			point_id = {$pointID}
    	";

    	return $this->_db->fetchOne($sql);
    }

    public function getServices($pointID, $tableLink)
    {
    	$sql = "
    		SELECT
    			*
    		FROM
    			{$tableLink}
    		WHERE
    			point_id = {$pointID}
    		ORDER BY
    			startdate
    	";

    	return $this->_db->fetchAll($sql);
    }

    public function getServiceInfo($tableLink, $ID, $pointID = NULL)
    {
        if ( ! $tableLink || ! $ID )
        {
            return array();
        }

        if ($tableLink == 'collacation')
        {
            $additionalColumns = "(
                SELECT
                    typename
                FROM
                    asrtypes
                WHERE
                    typename_value = '" . AsrHelp::COLLACATION_EQ_TYPE . "'
                    AND typename_id = LINK.equipment_type
            ) AS equipment_type_name,";
        }

        $sql = "
            SELECT
                LINK.*,
                PTS.*,
				PTS.sign_name,
                CLA.*,

                {$additionalColumns}

                TAR.servicetype_id,
                TAR.tarif_name,
                TAR.group_name,
                TAR.tablelink,
                TAR.limit,
                TAR.unlimit,
                TAR.tarif_price,
                TAR.speed,

                LINK.reg_pay - (LINK.reg_pay * LINK.discount / 100) AS reg_pay_total,

                (SELECT
                                servicetype_name
                            FROM
                                service_type AS ST
                WHERE
                                ST.servicetype_id = TAR.servicetype_id
                            ) AS servicetype_name,  -- Тип услуги

                (SELECT
                                label
                            FROM
                                point_statuses_view
                WHERE
                                code = PTS.statuscross
                            ) AS statuscross_label -- Статус услуги
            FROM
                    {$tableLink} AS LINK
            JOIN
                points AS PTS ON PTS.point_id = LINK.point_id
            JOIN
                clients AS CLA ON CLA.client_id = PTS.client_id
            LEFT JOIN
                tarifs AS TAR ON TAR.tarif_id = LINK.tarif_id
            WHERE
                LINK.id = {$ID}
        ";

        if ($pointID)
        {
            $sql .= " AND LINK.point_id = {$point_id} ";
        }

        $data = $this->_db->fetchRow($sql);

        $tarifComponentsModel = new TarifComponents();
        $data['tarif_components'] = $tarifComponentsModel->getComponentsByTarifID($data['tarif_id']);

        return $data;
    }

    public function verifyNeedCross($sid)
    {
        $sql = "select need_cross from service_type where servicetype_id = {$sid}";
        return $this->_db->fetchOne($sql);
    }

    public function getPointServicesCount($pointID)
    {
    	$sql = "
        	SELECT
        		count(point_id)
        	FROM
        		client_services
        	WHERE
        		point_id = {$pointID}
        ";

    	return $this->_db->fetchOne($sql);
    }

    public function searchNeddCrossService($point_id)
    {
        $sql = "
        SELECT
        	point_id
        FROM
        	client_services
        WHERE
        	point_id = {$point_id}
        	AND need_cross = 1
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getLogin($point_id)
    {
        $sql = "
        select u_login from points where point_id = {$point_id}
        ";
        return $this->_db->fetchOne($sql);
    }

    public function fetchRecordByID($pointID)
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return array();
        }

        $sql = "
            SELECT
                *
            FROM
                points
            WHERE
                point_id = {$pointID}
        ";

        return $this->_db->fetchRow($sql);
    }

    public function getServiceDates($tableLink, $ID)
    {
        $sql = "
            SELECT
                point_id
            FROM
                $tableLink
            WHERE
                id = {$ID}
        ";

        $pointID = $this->_db->fetchOne($sql);

        $fromSql = "
            SELECT
                startdate
            FROM
                $tableLink
            WHERE
                point_id = {$pointID}
            ORDER BY
                startdate
            LIMIT 1
        ";

        $result['startdate'] = $this->_db->fetchOne($fromSql);

        $toSql = "
            SELECT
                enddate
            FROM
                {$tableLink}
            WHERE
                point_id = {$pointID}
            ORDER BY
                enddate DESC
            LIMIT 1
        ";

        $result['enddate'] = $this->_db->fetchOne($toSql);
        return $result;
    }




    public function selectOldInformationAboutPoint($point_id)
    {
        /**
        *  Сам метод который нам нужен что бы получить
        *  информацию (старую) которую нужно изменить.
        */
        $sql = "select
                PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id) as client_type_id,
                (select name ||''|| address from ats_list
                where id = PTS.ats_id) as ats_name,

                (select name ||''|| ip_address from dslam_list
                where id = PTS.dslam_id) as dslam_name,

                (select label from point_statuses_view
                where code = PTS.statuscross) as statuscross,

                (select typename from asrtypes
                where typename_value = '1' and typename_id = PTS.country_id) as country_name

                from points as PTS
                where
                PTS.point_id = '{$point_id}'
        ";
        return $this->_db->fetchRow($sql);
    }

    public function saveNewData($newdata, $newlogin)
    {
        /**
        *  Метод который обновляет данные после редактирования
        *  в EditpointController - метод saveAction()
        */
        $sqltoupdate = "
        update points
            set
                phone = '{$newdata['phone']}',
                pcross = '{$newdata['pcross']}',
                connect_address = '{$newdata['connect_address']}',
                contact_name = '{$newdata['contact_name']}',
                country_id = '{$newdata['country_id']}',
                u_login = '{$newlogin}',
                pcross_owner = '{$newdata['pcross_owner']}',
				sign_name = '{$newdata['sign_name']}',
				post_sign_name = '{$newdata['post_sign_name']}'
            where
                point_id = '{$newdata['point_id']}'
        ";

        return $this->_db->fetchRow($sqltoupdate);
    }

    public function deleteDataPoint($pointID)
    {
    	$this->_db->beginTransaction();

    	$clientIDSql = "
    		SELECT
    			client_id
    		FROM
    			points
    		WHERE
    			point_id = {$pointID}
    	";

    	$clientID = $this->_db->fetchOne($clientIDSql);

    	$countPointsSql =  "
    		SELECT
    			count(point_id)
    		FROM
    			points
    		WHERE client_id = {$clientID}
    	";

    	$count = $this->_db->fetchOne($countPointsSql);

        $sqlPoints = "
            delete from points
                where point_id = {$pointID}
        ";

        $this->_db->fetchOne($sqlPoints);

        if ($count == 1)
        {
        	$sqlClients = "
        		delete from clients
        			where client_id = {$clientID}
        	";
        	$this->_db->fetchOne($sqlClients);
        }

        $this->_db->commit();
    }

    public function checkNumber($pointID, $countryID, $number)
    {
        if ( ! $number )
        {
            return false;
        }

        $sql = "
            SELECT
                point_id
            FROM
                points
            WHERE
                pcross = '{$number}'
                AND country_id = {$countryID}
        ";

        if ($pointID)
        {
            $sql .= "AND point_id != {$pointID}";
        }

        $point = $this->_db->fetchOne($sql);

        if ($point)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function getPointsInfo($pointIDs)
    {
    	if ( ! is_array($pointIDs) || count($pointIDs) == 0)
    	{
    		return false;
    	}

        $condition = "";

        $sql = "
            SELECT
                client_services.client_id,
                client_services.point_id,
                client_services.dslam_id,
                client_services.port_id,
                client_services.client_type_id,
                client_services.dslam_ip,
                client_services.portnumber AS port_number,
                client_services.speed AS portspeed,
                client_services.statuscross
            FROM
                client_services
            WHERE
                client_services.point_id IN (" . implode(',', $pointIDs) . ")
				AND client_services.speed not like '1'
        ";

        return $this->_db->fetchAll($sql);
    }

    public function setPwdFromContract($pwd, $login, $new_pwd, $point_id)
    {
        $sql1 = "
        update points set u_passwd  = '{$new_pwd}'
        where
            point_id = {$point_id}
		and
			u_login = '{$login}'
        ";

        $this->_db->fetchRow($sql1);

        $sql2 = "
			update radcheck set value = '{$new_pwd}' where username = '{$login}'
		";
		$this->_db->fetchRow($sql2);
    }

    public function setAtsBonus($data)
    {
    	$sql = "
			select ats_bonus(
								{$data['point_id']},
								'{$data['u_login']}',
 								'{$data['tablename']}',
								{$data['tarif_id']},
								{$data['bonusday']},
								{$data['day_in_selected_month']},
								'{$data['my_date']}'
							);
		";

		$this->_db->fetchAll($sql);
    }

	public function collaWithPereshet($data)
	{
		$days = date('t', strtotime($data['startdate_year']. "-". $data['startdate_month']. "-". $data['startdate_day']));

		$need_price = ($data['abon_price'] / $days) * ($days - $data['startdate_day']);
		$paidto = date('Y-m-01', strtotime('+1 month'));

		$sql_ballance = "
			update clients set ballance = ballance - {$need_price}
			where client_id = {$data['client_id']}
		";

		$sql_service = "
			update collacation set paidto = '{$paidto}', penable = true
			where
				point_id = {$data['point_id']}
			and
				now() between startdate and enddate
		";

		$sql_tran = "
			insert into transactions (trantype, summa, client_id, servicetype, commente, summas)
			values
				(
					1001,
					{$need_price},
					{$data['client_id']},
					7050,
					'Списание абон платы',
					dollar2sum({$need_price})
				)
		";
		$this->_db->fetchAll($sql_ballance);
		$this->_db->fetchAll($sql_service);
		$this->_db->fetchAll($sql_tran);

	}


	public function updateCrossState($data, $flag = null)
	{
        if($flag == 1)
        {
			if($data['resolved_code'] == "")
			{
				return null;
			}
			$sql = "
				UPDATE points
				SET
					status_before = statuscross,
					statuscross = {$data['resolved_code']},
					last_modified_date = now()
				WHERE
					point_id = {$data['point_id']}
			";
        }
        else
        {
        	$interval = $data['count_days']." day";
	        $sql = "
	            select * from backup_from_demand({$data['point_id']},{$data['count_days']}, '{$interval}', '{$data['notes']}')
	        ";
        }

		return $this->_db->fetchAll($sql);
	}

	public function continueService($pointID, $tableName, $isPayed = false)
	{
		$endDate = date('Y-01-01', strtotime('+1 year'));

		$sql = "
			SELECT
				id
			FROM
				{$tableName}
			WHERE
				point_id = {$pointID}
				AND current_date >= startdate
			ORDER BY startdate DESC
		";

		$ID = $this->_db->fetchOne($sql);

		if ($ID)
		{
			$startMonth = date("Y-m-01");

			if ($isPayed)
			{
				$startMonth = date("Y-m-01", strtotime('+1 month ' . $startMonth));
			}

			$sql = "
				UPDATE
					{$tableName}
				SET
					enddate = '{$endDate}',
					paidto='{$startMonth}',
					is_deleted = false
				WHERE
					id = {$ID}
			";

			$this->_db->query($sql);
		}
	}

	public function continueServices($pointID, $isPayed = false)
	{

		$tables = array('adsl', 'wifi', 'tasix', 'vpn', 'dialup', 'hosting', 'ngn', 'collacation', 'additional_services');

		foreach ($tables as $table)
		{
			$this->continueService($pointID, $table, $isPayed);
		}
	}

	public function getUncrossClients()
	{
		$sql = "
			SELECT
				points.*,
				clients.client_name,
				clients.ballance,
				phone_hub_list.name AS phone_hub_name
			FROM
				points
			JOIN
				clients ON clients.client_id = points.client_id
			LEFT JOIN
				ats_list ON ats_list.id = points.ats_id
			LEFT JOIN
				phone_hub_list ON phone_hub_list.id = ats_list.phone_hub_id
			WHERE
				points.statuscross = -25
			ORDER BY
				phone_hub_name, clients.client_name
		";
		return $this->_db->fetchAll($sql);
	}

	public function showCountCrossServices($pid)
	{
		$sql = "
			select count(point_id) as counter from client_services where point_id = {$pid} and need_cross = 1
		";
		return $this->_db->fetchOne($sql);
	}

	public function setNewLogin($pid, $login)
	{
		$sql = "
			update points set u_login = '{$login}' where point_id = {$pid}
		";
		return $this->_db->fetchAll($sql);
	}

	public function closeIpByPoint($point_id)
	{
		$sql = "
			update point_ip_addresses set end_date = now()
			where
				point_id = {$point_id}
		";
		$this->_db->fetchAll($sql);
	}

	public function logStatusCross($point_id)
	{
		$sql_status = "
			select statuscross from points where point_id = {$point_id}
		";
		$old_status = $this->_db->fetchOne($sql_status);

		$sql_update = "
			update points set status_before = {$old_status} where point_id = {$point_id}
		";
		$this->_db->fetchAll($sql_update);
	}

	public function getBeforeStatusCross($point_id)
	{
		$sql = "
			select status_before from points where point_id = {$point_id}
		";
		return $this->_db->fetchOne($sql);
	}

	public function setTestDaysPeriod($point_id, $commente)
	{
		if($point_id == "" or $commente == "" )
		{
			return;
		}

		$sql = "
			update points set test_days = now() + INTERVAL '4 days', notes_for_test = '{$commente}' where point_id = {$point_id}
		";

		$this->_db->fetchAll($sql);

		return 1;
	}

    public function getInfo($pointID, $serviceTable)
    {
    	$info = $this->_db->select()->from($serviceTable)
    				->join('points', "points.point_id = {$serviceTable}.point_id", array('pcross'))
    				->join('clients', "clients.client_id = points.client_id", array('client_name'))
    				->where("{$serviceTable}.point_id = {$pointID}")
					->where("{$serviceTable}.startdate <= now()")
					->where("{$serviceTable}.enddate > now()")
    				->query()->fetchAll();

    	if (is_array($info) && $info[0])
    	{
    		return $info[0];
    	}
    	else
    	{
    		return array();
    	}
    }

	public function getFirstPcross($clientID)
    {
    	$sql = "
    		SELECT
    			pcross
    		FROM
    			points
    		WHERE
    			client_id = {$clientID}
    			AND pcross IS NOT NULL
    			AND pcross != ''
    		LIMIT 1
    	";

    	return $this->_db->fetchOne($sql);
    }

    public function rollbackStatusCross($pointID)
    {
    	if ( ! $pointID )
    	{
    		return false;
    	}

    	$data['statuscross'] = new Zend_Db_Expr('status_before');
    	$this->update($data, "point_id = {$pointID}");
    	return true;
    }

	public function getClientPoints($clientID, $status = false)
    {
    	if ( ! $clientID )
    	{
    		return null;
    	}

    	$select = $this->_db->select()->from('points')
    		->where('client_id = ?', $clientID);

    	if ($status !== false)
    	{
    		$status = $status ? $status : '0';
    		$select = $select->where('statuscross = ?', $status);
    	}

    	return $this->_db->fetchAll($select);
    }
}