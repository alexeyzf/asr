<?php
require_once('BaseModel.php');


class KitModel extends BaseModel
{
    protected $_name = 'tarifs';

    public function getAllUnlimTarifsCorpBySpeed($speed, $is_stream) //64
    {
    	$speed = $speed. "/";

        $group_name = "";

        if($is_stream == 1)
        {
            $group_name = "%private%";
            $servicetype = "3000";
            $addStr = " and isface = 1 ";
        }
        else
        {
            $group_name = "%not_gara%";
            $servicetype = "7000";
        }

    	$sql = "
			select * from tarifs
			where
				group_name not like '{$group_name}'
			and
				servicetype_id = {$servicetype}
			and
				speed like '{$speed}%'
                                {$addStr}
			order by speed
		";
	$allTarifs = $this->_db->fetchAll($sql);

	return $allTarifs;
    }

	public function getTarifsByClientTypeID($is_stream)
	{
		if($is_stream == 1)
        {
            $servicetype = "3000";
            $addStr = " and isface = 1 ";
        }
        else
        {
            $addStr = " and isface = 0 ";
            $servicetype = "7000";
        }

        $sql = "
			select * from tarifs
			where
				servicetype_id = {$servicetype}
                {$addStr}
			order by tarif_name
		";
		$allTarifs = $this->_db->fetchAll($sql);
		return $allTarifs;
	}

	public function getClientsByTarifAllStream($year, $month, $flag)
	{
		$dateNeed = $year."-". $month."-01 00:00:00";

		$sql_all = "
				select
                                    bind.*,
                                    (
                                            CASE WHEN bind.t_limit = 0 THEN
                                                    1000000000
                                            ELSE
                                                    bind.t_limit
                                            END
                                    ) as t_limit
                                from
				(select
					A.*,
					(select tarif_name from tarifs where tarif_id = A.tarif_id) as tarif_name,
					(select tarif_price from tarifs where tarif_id = A.tarif_id) as tarif_price,
					(select group_name from tarifs where tarif_id = A.tarif_id) as group_name,
					(select speed from tarifs where tarif_id = A.tarif_id) as speed,
					(select statuscross from points where point_id = A.point_id) as statuscross,
					(select
		 				(
							CASE WHEN stream_traffic.traffic_out > stream_traffic.traffic_in THEN
									stream_traffic.traffic_out
								ELSE
									stream_traffic.traffic_in
							END
						) as traffic_out

					 from stream_traffic where username = A.login
						and
							to_char('{$dateNeed}'::timestamp, 'YYYY-MM') = to_char(\"date\", 'YYYY-MM')
					) as traffic_out,
					(select client_type_id from clients, points
						where
							clients.client_id = points.client_id
						and
							points.point_id = A.point_id limit 1) as ctype,
					(select \"limit\" from tarifs where tarif_id = A.tarif_id) as t_limit
				from adsl  as A
				where
				    '{$dateNeed}' between A.startdate and A.enddate - INTERVAL '1 seconds'
				) as bind
				where
				    bind.statuscross = 25
				and
				    bind.ctype = 1
		";

		if($flag == 'private')
		{
			$sql_all .= "
				and bind.group_name not like '%private%'
			";
		}
		$sql_all .= " order by bind.tarif_id";

		return $this->_db->fetchAll($sql_all);
	}

    public function getClientsByStreamNovaTarif($year, $month)
    {
        $startMoment = "{$year}-{$month}-01 00:00:00";
        $startDay = "{$year}-{$month}-01";

        $sql = "
            SELECT
                A.*,
                T.tarif_name, TP.tarif_price, T.group_name,
                TP.speed_down, TP.speed_up, TP.speed_down AS speed,
                CASE
                    WHEN ST.traffic_out >= ST.traffic_in THEN ST.traffic_out
                    ELSE ST.traffic_in
                END AS traffic_out
            FROM
                adsl AS A
                INNER JOIN points P
                    ON A.point_id = P.point_id
                INNER JOIN clients AS C
                    ON P.client_id = C.client_id
                INNER JOIN tarifs AS T
                    ON A.tarif_id = T.tarif_id
                INNER JOIN stream_traffic AS ST
                    ON A.login = ST.username AND date >= '{$startDay}'
                INNER JOIN tarif_properties AS TP
                    ON A.id = TP.service_id
            WHERE
                '{$startMoment}' between A.startdate and A.enddate - INTERVAL '1 seconds'
                AND C.client_type_id = 1
                AND P.statuscross = 25
            ORDER BY to_number(TP.speed_down, '999999')
        "; 

        return $this->_db->fetchAll($sql);
    }

    public function getClientsByTarif($year, $month, $arrTarifs, $is_stream)
    {
    	$pointIPAdressModel = new PointIpAddresses();
		$arr = array();
        $dateNeed = $year."-". $month."-01 00:00:00";
        //$dateNeedEnd   = $year."-". $month. "-". date('t', strtotime($dateNeedStart)). " 23:59:59";

		foreach($arrTarifs as $value)
		{
			$sql = "
                            select * from
                            (select
                                    A.*,
                                    (select statuscross from points where point_id = A.point_id) as statuscross
                            from adsl  as A
                            where
                                    A.tarif_id = {$value['tarif_id']}
                            and
                                    '{$dateNeed}' between A.startdate and A.enddate - INTERVAL '1 seconds'
                            ) as bind
                            where
                                    bind.statuscross = 25
			";

			$serviceData = $this->_db->fetchAll($sql);

			for($i = 0; $i < count($serviceData); $i++)
			{
                            if($is_stream == 0)
                            {
								$ips = $pointIPAdressModel->getPointIpAddresses($serviceData[$i]['point_id']);

                                if ($ips && is_array($ips))
                                {
                                        $serviceData[$i]['ip_address'] = implode(' ', $ips);
                                }

                                $serviceData[$i]['traffic_bytes'] = $this->getTraffic($month, $year, $serviceData[$i]['point_id']);
                            }
                            elseif($is_stream == 1)
                            {
                                $serviceData[$i]['traffic_bytes'] = $this->getStreamTraffic($month, $year, $serviceData[$i]['point_id']);
                            }
			}

			foreach($serviceData as $value)
			{
				array_push($arr, $value);
			}
		}

		return $arr;
    }

    public function getTraffic($month, $year, $pid)
    {
    	$dateNeed = $year."-". $month."-01";

    	$sql = "
				select sum(traffic) from corp_traffic
				where
					point_id = {$pid}
				and
					to_char('{$dateNeed}'::date,'YYYY-MM') = to_char(date::date,'YYYY-MM');
		";
		return  $this->_db->fetchOne($sql);
    }

    public function getStreamTraffic($month, $year, $pid)
    {

        $dateNeed = $year."-". $month."-01";

    	$sql = "
                select sum(traffic_in) as traffic_in, sum(traffic_out) as traffic_out  from stream_traffic
                where
                        username = (select u_login from points where point_id = {$pid})
                and
                        to_char('{$dateNeed}'::date,'YYYY-MM') = to_char(date::date,'YYYY-MM');
	";
        $traffic =  $this->_db->fetchRow($sql);
        if($traffic['traffic_in'] > $traffic['traffic_out'])
        {
            return $traffic['traffic_in'];
        }
        else
        {
            return $traffic['traffic_out'];
        }
    }
}
?>