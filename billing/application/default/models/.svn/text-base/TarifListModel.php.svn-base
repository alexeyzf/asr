<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Zend/Db/Table.php');

class TarifListModel extends Zend_Db_Table
{
    protected $_name = 'tarifs';
    protected $_sequence = 'test_tarifs_seq';

    const RESERV_STREAM = 49;
    const RESERV_CORP = 50;
    const STREAM = 3000;

	public function getServiceFuture($tarifID, $sid, $tablename, $pointID)
	{
		$sql = "
			select max(id) from {$tablename} where point_id = {$pointID}
		";

		$result = $this->_db->fetchOne($sql);
		if($result != $sid)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function getStreamUnlim()
	{
		$sql = "
			select * from tarifs
			where
				isface = 1
			and
				\"limit\" = 0
			and
				servicetype_id = 3000
		";
		return $this->_db->fetchAll($sql);
	}

	public function getTarifDeliveryInvoice()
	{
		$sql = "
                    select tarif_id from tarifs where servicetype_id = 9999
		";
		return $this->_db->fetchOne($sql);
	}

    public function getTableLink($service_id)
    {
        /**
        *  Метод возвращает наименование таблицы для указанной усл.
        */
        $sql = "
        select  tablelink from service_type as ST
        where
            ST.servicetype_id  = {$service_id}
        ";
        return $this->_db->fetchOne($sql);
    }

    public function getServiceTarifs($serviceTypeID, $cityID = 0)
    {
        if ( ! $serviceTypeID )
        {
            return array();
        }

        if ( ! $cityID )
        {
        	$cityID = '0';
        }

        if ($serviceTypeID == 7000 or $serviceTypeID == 7100)
        {
        	$serviceTypeFilter = "(tarifs.servicetype_id = 7000
        		OR tarifs.servicetype_id = 7100
        		OR (tarifs.servicetype_id = 3000 AND tarifs.group_name = 'corp_stream'))";
        }
        else
        {
        	$serviceTypeFilter = "tarifs.servicetype_id = {$serviceTypeID}";
        }

        return $this->_db->select()->from('tarifs')
        		->join('tarifs_in_cities',
        			   "tarifs_in_cities.tarif_id = tarifs.tarif_id
        				AND tarifs_in_cities.city_id = {$cityID}", array())
        		->where("{$serviceTypeFilter}
        				 AND tarifs.is_deleted = false")
        		->order('tarif_name')
        		->query()->fetchAll();
    }

    /**
     * Gets first not nullable speed by point id
     *
     * @param integer $pointID - Point ID
     */
    public function getSpeed($pointID)
    {
        $sql = "
            SELECT
                speed
            FROM
                client_services
            WHERE
                client_services.point_id = {$pointID}
                AND speed IS NOT NULL
        ";

        return $this->_db->fetchOne($sql);
    }

    public function selectOldTarif($point_id, $tablename, $id)
    {
        $sql = "
            select * from {$tablename} as LINK
            where
                LINK.point_id = {$point_id}
            and
                LINK.id = {$id} limit 1
            ";
        $var = $this->_db->fetchRow($sql);
        return $var;
    }

    public function saveTarif($data, $id = NULL)
    {
        $selectTableLink = "
       		SELECT
       			tablelink
       		FROM
       			service_type
        	WHERE
            	servicetype_id = {$data['servicetype_id']}
        	LIMIT 1
        ";

        $tablelink         = $this->_db->fetchOne($selectTableLink);
        $data['tablelink'] = $tablelink;

        $columns = $this->_getCols();

        foreach ($data as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($data[$key]);
            }
        }

        $id = intval($id);

        if ($id)
        {
            //$oldData['enddate'] = $data['enddate'];
            $this->update($data, "tarif_id = {$id}");
        }
        else
        {
            unset($data['id']);
            $id = $this->insert($data);
        }
        return $id;
    }

    public function getListTarif($tarif_id = "")
    {
        $sql = "
        SELECT
            TAR.*,
            (select servicetype_name from service_type
                where servicetype_id = TAR.servicetype_id) as servicetype_name
        FROM
        	tarifs as TAR
        ";

        if($tarif_id)
        {
            $sql .= "  where  TAR.tarif_id = {$tarif_id} order by TAR.servicetype_id";
        }
        else
        {
            $sql .= "  order by TAR.servicetype_id, TAR.tarif_name";
        }


        return $this->_db->fetchAll($sql);
    }

	public function getTarifNameFromPoint($pointID, $tablename)
	{
		$sql = "
			select
				(select tarif_name from tarifs where tarif_id = CS.tarif_id) as tarif_name
			from client_services  as CS
			where
				CS.point_id = {$pointID}
			and
				CS.tablename = '{$tablename}'
			and
				CS.servicetype_id  <> 9999
		";
		return $this->_db->fetchOne($sql);
	}

    public function getTarifData($tarifID)
    {
    	if ( ! $tarifID )
    	{
    		return array();
    	}

        $sql = "
        	SELECT
            	TAR.*,
            	service_type.servicetype_name
        	FROM
            	tarifs as TAR
            JOIN
                service_type ON service_type.servicetype_id = TAR.servicetype_id
        	WHERE
        		TAR.tarif_id = {$tarifID}
        ";

        return $this->_db->fetchRow($sql);
    }

    public function saveComponent($data, $update = "")
    {
        if($update)
        {
            $sql = "
            update tarif_components set
                component_name = '{$data['component_name']}',
                traffic_excess = {$data['traffic_excess']},
                starttime      = '{$data['starttime']}',
                endtime        = '{$data['endtime']}',
                weekday        = '{$data['weekday']}'
            where
                component_id = {$update}
            ";
            return $this->_db->fetchRow($sql);
        }
        else
        {
            $sql = "
            insert into tarif_components
                (tarif_id, component_name, traffic_excess,
                starttime, endtime, weekday)
            values (
                {$data['tarif_id']},
                '{$data['component_name']}',
                {$data['traffic_excess']},
                '{$data['starttime']}',
                '{$data['endtime']}',
                '{$data['weekday']}'
            )
            ";
            return $this->_db->fetchAll($sql);
        }
    }

    public function getTarifComponent($tarif_id, $where_st = "")
    {
        $sql = "
        select
            TC.*,
            (select servicetype_name from service_type as ST, tarifs as TAR
            where
                ST.servicetype_id = TAR.servicetype_id
            and
                TAR.tarif_id = TC.tarif_id
            ) as servicetype_name,

            (select tarif_name from tarifs as TAR
                where TAR.tarif_id = TC.tarif_id) as tarif_name


        from tarif_components as TC where TC.tarif_id = {$tarif_id}
        ";

        if($where_st)
        {
            $sql .= " and TC.component_id = {$where_st}";
            return $this->_db->fetchRow($sql);
        }
        return $this->_db->fetchAll($sql);
    }

    public function deleteComponent($component_id)
    {
        $sql = "
        delete from tarif_components where component_id = {$component_id}
        ";
        $count_rows = $this->_db->delete('tarif_components', 'component_id = '.$component_id);

        if($count_rows)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Gets tarif price due to option
     *
     * @param integer $tarifID
     * @param integer $option - Option for calculate tarif price
     * 0 - ordinal, full month
     * 1 - from this day to end of month
     * 2 - from start of month to this day
     */
    public function getTarifPrice($tarifID, $option, $startDate = null)
    {
        $tarifID = intval($tarifID);

        if ( ! $tarifID )
        {
            return 0;
        }

        $tarif = $this->fetchRow("tarif_id = {$tarifID}"); // надо рефакторить

        if ( ! $tarif || ! $tarif->tarif_price )
        {
            return 0;
        }

        return $this->calculateTarifPrice($tarif->tarif_price, $option, $startDate);
    }

    public function calculateTarifPrice($tarifPrice, $option, $startDate)
    {
    	if ( ! $startDate )
    	{
    		$startDate = 'now';
    	}

    	switch ($option)
        {
            case 0:
                return $tarifPrice;

            case 1:
                $day = date('j', strtotime($startDate));
                $daysInMonth = date('t', strtotime($startDate));
                return ($daysInMonth - $day + 1) * $tarifPrice / $daysInMonth;

            case 2:
                $day = date('j', strtotime($startDate)) -  1;
                $daysInMonth = date('t', strtotime($startDate));
                return $day * $tarifPrice / $daysInMonth;
        }
    }

    public function getTarifChangeHistory($point_id, $tablename)
    {
    	$sql = "
			select
				LINK.*,
				(select tarif_name from tarifs where tarif_id = LINK.tarif_id) as tarif_name
			from {$tablename} as LINK
			where
				LINK.point_id = {$point_id}
                        order by LINK.startdate desc
		";
		return $this->_db->fetchAll($sql);
    }

    public function getDataForSpeedReport($date, $service)
    {
    	$sql = "
    		SELECT
    			country_id,
    			speed,
    			client_type_id,
    			COUNT(DISTINCT point_id) AS points_count
    		FROM
    			cross_service_report
    		WHERE
    			servicetype_id IN {$service}
    			AND '{$date}' >= startdate
    			AND '{$date}' < enddate
    			AND speed != 'резервирование'
    			AND speed IS NOT NULL
    			AND speed != ''
    			AND speed != '0'
    			AND (client_type_id = 1 OR is_forced = true)
    		GROUP BY
    			country_id, speed, client_type_id
    		ORDER BY
    			country_id, char_length(speed), speed, client_type_id
    	";

    	return $this->_db->fetchAll($sql);
    }

    public function getDataForTarifReport($date, $clientType)
    {
    	$sql = "
    		SELECT
    			tarif_name,
    			COUNT(DISTINCT point_id) AS points_count
    		FROM
    			cross_service_report
    		WHERE
    			client_type_id = {$clientType}
    			AND (client_type_id = 1 OR is_forced = true)
    			AND '{$date}' >= startdate
    			AND '{$date}' < enddate
    		GROUP BY
    			tarif_name
    		ORDER BY
    			tarif_name
    	";

    	return $this->_db->fetchAll($sql);
    }

    public function getActiveTarifs($clientType)
    {
    	$sql = "
    		SELECT DISTINCT
    			tarif_name
			FROM
				cross_service_report
			WHERE
				client_type_id = {$clientType}
    	";
    	return $this->_db->fetchAll($sql);
    }

    public function getActiveTarifsByNewConnections($clientType, $start, $end)
    {
        $sql =
        "
            SELECT DISTINCT
                SAS.tarif_id,
                (SELECT
                    T.tarif_name
                        FROM
                        tarifs T
                        WHERE
                        T.tarif_id = SAS.tarif_id) AS service_name
            FROM
                (SELECT
                    client_id, client_type_id, client_dateagree
                 FROM clients
                 UNION ALL
                    SELECT
                        client_id, client_type_id, client_dateagree
                    FROM clients_arhiv) CLA
                INNER JOIN (SELECT
                                tarif_id, client_id, reg_pay
                            FROM
                                client_services_all
                            UNION ALL
                            SELECT
                                tarif_id, client_id, reg_pay
                            FROM
                                client_services_all_arhiv) SAS
                ON CLA.client_id = SAS.client_id
            WHERE
                CLA.client_type_id = {$clientType}
                AND CLA.client_dateagree BETWEEN '{$start}' AND '{$end}'
                AND SAS.reg_pay IS NOT NULL
            ORDER BY SAS.tarif_id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getFullInfoForPeriod($startDate, $endDate, $clientType, $tarifId=null)
    {
    	$sql = "
    		SELECT
                SUM(CASE
                    WHEN CSA.currency = 'USD' THEN COALESCE(TP.tarif_price, CSA.tarif_price)
                            WHEN CSA.currency = 'UZS' THEN sum2dollar(COALESCE(TP.tarif_price, CSA.tarif_price), '{$endDate}')
                    END) AS full_sum,
                COUNT(*) As full_amount
            FROM
                (SELECT * FROM client_services_all
                 UNION ALL
                 SELECT * FROM client_services_all_arhiv) CSA
                LEFT JOIN adsl A
                    ON A.point_id = CSA.point_id
                    AND CSA.startdate = A.startdate
                    AND CSA.enddate = A.enddate
                LEFT JOIN tarif_properties TP
                    ON A.id = TP.service_id
            WHERE
                CSA.startdate <= '{$startDate}'
      			AND CSA.enddate > '{$startDate}'
                AND ((now() BETWEEN CSA.startdate AND CSA.enddate AND NOT CSA.is_deleted)
                     OR (now() NOT BETWEEN CSA.startdate AND CSA.enddate AND CSA.is_deleted))
                AND CSA.client_type_id = {$clientType}
    	";
        if ($tarifId)
        {
            $sql .= "
                AND CSA.tarif_id = {$tarifId}
            ";
        }

    	return $this->_db->fetchRow($sql);
    }

    public function getDataForTarifSummaryReport($startDate, $endDate, $clientType)
    {
    	$sql = "
    		SELECT
                CSA.tarif_name,
                SUM(CASE
                    WHEN CSA.currency = 'USD' THEN COALESCE(TP.tarif_price, CSA.tarif_price)
                    WHEN CSA.currency = 'UZS' THEN sum2dollar(COALESCE(TP.tarif_price, CSA.tarif_price), '{$endDate}')
                    END) AS price,
                COUNT(*) AS amount
            FROM
                (SELECT * FROM client_services_all
                 UNION ALL
                 SELECT * FROM client_services_all_arhiv) CSA
                LEFT JOIN adsl A
                    ON A.point_id = CSA.point_id
                    AND CSA.startdate = A.startdate
                    AND CSA.enddate = A.enddate
                LEFT JOIN tarif_properties TP
                    ON A.id = TP.service_id
            WHERE
                CSA.startdate <= '{$startDate}'
      			AND CSA.enddate > '{$startDate}'
                AND ((now() BETWEEN CSA.startdate AND CSA.enddate AND NOT CSA.is_deleted)
                     OR (now() NOT BETWEEN CSA.startdate AND CSA.enddate AND CSA.is_deleted))
                AND CSA.client_type_id = {$clientType}
            GROUP BY CSA.tarif_name
            ORDER BY CSA.tarif_name
		";

    	return $this->_db->fetchAll($sql);
    }

    public function getDataForNovaTarifSummaryReport($startDate)
    {
    	$sql = "
    		SELECT
                Info.tarif_name,
                sum(Info.price) as price,
                COUNT(*) AS amount
            FROM
                (SELECT
                    T.tarif_name || '-' || TP.speed_down || '/' || TP.speed_up AS tarif_name,
                    TP.tarif_price AS price,
                    A.startdate, A.enddate
                FROM
                    adsl AS A
                    INNER JOIN tarifs AS T
                        ON A.tarif_id = T.tarif_id
                    INNER JOIN tarif_properties AS TP
                        ON A.id = TP.service_id) AS Info
            WHERE
                Info.startdate <= '{$startDate}'
                    AND Info.enddate > '{$startDate}'
            GROUP BY Info.tarif_name, Info.price
            ORDER BY Info.tarif_name
		";
                    
    	return $this->_db->fetchAll($sql);
    }

    public function getDataForTarifChangeReport($month, $year, $clientType)
    {
    	$startMonth = date('Y-m-d', strtotime('01.' . $month . '.' . $year));
    	$endMonth = date('Y-m-t', strtotime('01.' . $month . '.' . $year));

    	if ( ! $clientType ) {
    		$clientType  = '0';
    	}

    	$sql = "
    		SELECT
				c2.tarif_name AS from_tarif_name,
				c2.tarif_id as from_tarif_id,
				c1.tarif_id as to_tarif_id,
    			c1.tarif_name AS to_tarif_name,
    			COUNT(DISTINCT c1.point_id) AS points_count
    		FROM
    			cross_service_report AS c1,
    			cross_service_report AS c2
    		WHERE
    			c1.startdate = c2.enddate
    			AND c1.point_id = c2.point_id
    			AND c1.servicetype_id = c2.servicetype_id
    			AND c1.tarif_id != c2.tarif_id
    			AND c1.client_type_id = {$clientType}
    			AND (c1.client_type_id = 1 OR c1.is_forced = true)
    			AND c1.startdate between '{$startMonth}' AND '{$endMonth}'
                -- исключаем нову
                AND C1.tarif_id <> 7 AND C2.tarif_id <> 7
    		GROUP BY
    			to_tarif_name, from_tarif_name, from_tarif_id, to_tarif_id
    		ORDER BY
    			points_count desc
    	";

    	return $this->_db->fetchAll($sql);
    }

    public function getDataForNovaTarifChangeReport($month, $year)
    {
        $startMonth = date('Y-m-d', strtotime('01.' . $month . '.' . $year));
    	$endMonth = date('Y-m-t', strtotime('01.' . $month . '.' . $year));

        $sql = "
            SELECT
                FR.tarif_name AS from_tarif_name,
                FR.tarif_id AS from_tarif_id,
                FR.speed AS from_speed,
                FR.price AS from_price,

                TR.tarif_name AS to_tarif_name,
                TR.tarif_id AS to_tarif_id,
                TR.speed AS to_speed,
                TR.price AS to_price,

                COUNT(DISTINCT TR.point_id) AS points_count
            FROM
                (SELECT -- новые тарифы и информация о них
                    P.point_id, A.tarif_id, A.startdate, A.is_forced, T.servicetype_id,
                    CASE
                        WHEN TP.speed_down IS NOT NULL THEN TP.speed_down || '/' || TP.speed_up
                        ELSE T.speed
                    END AS speed,
                    CASE
                        WHEN TP.speed_down IS NOT NULL THEN T.tarif_name || '-' || TP.speed_down || '/' || TP.speed_up
                        ELSE T.tarif_name
                    END AS tarif_name,
                    CASE
                        WHEN TP.tarif_price IS NOT NULL THEN TP.tarif_price
                        ELSE T.tarif_price
                    END AS price
                 FROM
                    (SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) AS C
                    INNER JOIN (SELECT * FROM points UNION ALL SELECT * FROM points_arhiv) AS P
                        ON C.client_id = P.client_id
                    INNER JOIN adsl AS A
                        ON A.point_id = P.point_id
                    INNER JOIN tarifs AS T
                        ON A.tarif_id = T.tarif_id
                    LEFT JOIN tarif_properties AS TP
                        ON A.id = TP.service_id
                    WHERE
                        C.client_type_id = 1
                ) AS TR
                INNER JOIN
                (SELECT -- старые тарифы и информация о них
                    P.point_id, A.tarif_id, A.enddate, T.servicetype_id,
                    CASE
                        WHEN TP.speed_down IS NOT NULL THEN TP.speed_down || '/' || TP.speed_up
                        ELSE T.speed
                    END AS speed,
                    CASE
                        WHEN TP.speed_down IS NOT NULL THEN T.tarif_name || '-' || TP.speed_down || '/' || TP.speed_up
                        ELSE T.tarif_name
                    END AS tarif_name,
                    CASE
                        WHEN TP.tarif_price IS NOT NULL THEN TP.tarif_price
                        ELSE T.tarif_price
                    END AS price
                 FROM
                    (SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) AS C
                    INNER JOIN (SELECT * FROM points UNION ALL SELECT * FROM points_arhiv) AS P
                        ON C.client_id = P.client_id
                    INNER JOIN adsl AS A
                        ON A.point_id = P.point_id
                    INNER JOIN tarifs AS T
                        ON A.tarif_id = T.tarif_id
                    LEFT JOIN tarif_properties AS TP
                        ON A.id = TP.service_id
                    WHERE
                        C.client_type_id = 1
                ) AS FR
                    ON TR.startdate = FR.enddate
                    AND TR.point_id = FR.point_id
                    AND (TR.tarif_id != FR.tarif_id OR TR.speed <> FR.speed)
                    AND TR.servicetype_id = FR.servicetype_id
            WHERE
                TR.startdate between '{$startMonth}' AND '{$endMonth}'
                AND 7 IN (TR.tarif_id, FR.tarif_id) -- нас интересует только Nova
            GROUP BY from_tarif_id, from_speed, from_tarif_name, from_price, to_tarif_id, to_speed, to_tarif_name, to_price
            ORDER BY from_tarif_id, to_tarif_id
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getServiceTarifsReport($tablename, $is_stream, $tarif_id = null, $country = null)
    {

        if($tarif_id != "")
        {

            $sql = "
                select * from tarifs where tarif_id = {$tarif_id}
            ";

            return $this->_db->fetchRow($sql);
        }
        else
        {
                $sql = "
                 select
                     bind.*,
                     (select tarif_id from tarifs where tarif_name = bind.tarif_name limit 1) as tarif_id
                     from
                     ( select
                     (select tarif_name from tarifs where tarif_id = A.tarif_id) as tarif_name,
                     count(A.point_id) as tt
                     from {$tablename} as A, points as PTS, clients as CLA
                     where
                     A.tarif_id in (
                     select tarif_id from tarifs
                     where
                      isface = {$is_stream}
                     )
                 and
                     now() between startdate and enddate
                 and
                     A.is_deleted <> true
                 and
                     A.point_id = PTS.point_id
                 and
                     PTS.client_id = CLA.client_id
                 and
                     PTS.country_id = {$country}
                 group by tarif_name
                 order by tarif_name) as bind ";

                 return $this->_db->fetchAll($sql);
        }

    }

    public function getServiceTarifsReportClientDetails($tablename, $tarif_id, $isface)
    {

        if($isface == "1")
        {
            $isface = " bind.isface  in ({$isface})";
        }
        elseif($isface == "0")
        {
            $isface = " bind.isface  in ({$isface}, 3)";
        }

        //
        $sql = "
            select * from
            (select
                    (select clients.client_name from clients, points
                            where clients.client_id = points.client_id
                            and
                                  points.point_id = A.point_id) as client_name,
                    (select clients.client_id from clients, points
                            where clients.client_id = points.client_id
                            and
                                  points.point_id = A.point_id) as client_id,
                    A.*,
                    (select isface from tarifs where tarif_id = A.tarif_id) as isface,
					(select tarif_name from tarifs where tarif_id = A.tarif_id) as tarif_name,
					(select u_login from points where point_id = A.point_id limit 1) as u_login
            from {$tablename} as A, tarifs as TAR
            where
                    A.tarif_id = TAR.tarif_id
            and
                    TAR.tarif_id = {$tarif_id}
            and
                    now() between A.startdate and A.enddate
            and
                    A.is_deleted <> true) as bind
            where
                    bind.client_name is not null
            and
                    {$isface}
        ";

        $data = $this->_db->fetchAll($sql);

        if($tablename == "wifi")
        {
                for($i = 0; $i < count($data); $i++)
                {
                        $data[$i]['sam_traff'] = $this->getTrafficOldCorp($data[$i]['point_id']);
                }
        }
        return $data;
    }

    public function getTrafficOldCorp($point_id)
    {
    	$sql = "
			select sum(traffic) from corp_traffic
			where
				point_id = {$point_id}
			and
				to_char(now(), 'YYYY') = to_char(date, 'YYYY');
		";
		return $this->_db->fetchOne($sql);
    }

    public function getDublicateTarifs()
    {
    	$sql = "
    		SELECT
				tarif_price,
				isface,
				servicetype_id,
				tablelink,
				speed,
				starttime,
				endtime,
				\"limit\",
				unlimit,
				count(tarif_id) AS tarif_count
			FROM
				tarifs
			GROUP BY
				tarif_price,
				isface,
				servicetype_id,
				tablelink,
				speed,
				starttime,
				endtime,
				\"limit\",
				unlimit
			HAVING
				count(tarif_id) > 1
			ORDER BY
				tablelink ASC, speed DESC, tarif_count DESC
    	";

    	return $this->_db->fetchAll($sql);
    }

    public function doBulkTarifUpdate($oldTarifId, $newTarifId)
    {
        $sql = "SELECT mass_tarif_change({$oldTarifId}, {$newTarifId})";
        return $this->_db->fetchOne($sql);
    }
}
