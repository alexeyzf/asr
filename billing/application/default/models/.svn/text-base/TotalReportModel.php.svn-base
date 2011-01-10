<?php
require_once('Zend/Db/Table.php');

class TotalReportModel extends Zend_Db_Table
{
    protected $_name = 'result_report';

    private $new_clients_corp;
    private $new_clients_stream;

    private $corp_modems;
    private $stream_modems;


	public function getReportTotal($date, $month, $year, $is_face)
	{
		$selected_date = $date . "-01";
		$startDate = date('Y-m-01', strtotime('-1 month', strtotime($selected_date)));
		$endDate 	 = date("Y-m-01", strtotime($selected_date));

        if( date("m") == $month )
        {
			$startDate 	 = date("Y-m-01", strtotime($selected_date));
			$endDate 	 = date("Y-m-t", strtotime($startDate));
        	// Всего пользователей
			$result = $this->getAllClients($is_face, $startDate, $endDate);

			foreach($result as $key => $value)
			{
				foreach($value as $key => $value)
				{
					$totalResult[$key] = $value;
				}
			}

			unset($result);
			// END

			// Всего активных пользователей
			$totalResult['payed_clients'] = $this->payedClients($is_face, $startDate, $endDate);


			// Новые клиенты
			$totalResult['new_clients'] = $this->newClients($is_face, $startDate, $endDate);


			// Отключенные
			$totalResult['deleted'] = $this->uncrossedClients($is_face, $startDate, $endDate);


			// Количество проданных модемов
            $result = $this->sellModems($is_face, $startDate, $endDate);
			$totalResult['sell_modems'] = $result['sell_modems'];
            $totalResult['sell_modems_price'] = $result['sell_modems_price'];
            unset($result);

			// Количество клиентов со своим модемом
			$totalResult['without_modems'] = $this->withoutModems($is_face, $startDate, $endDate);


			// Фин. данные
			$result = $this->getAllAbonTran($is_face, $startDate, $endDate);

			foreach($result as $key => $value)
			{
				$totalResult[$key]  = $value;
			}
			unset($result);
			// End


			// Не дополученная абон плата
			$result = $this->notReceived($is_face, $startDate, $endDate);
			foreach($result as $key => $value)
			{
				$totalResult[$key]  = $value;
			}
			unset($result);
			//End

            //Количество точек, ушедших в заявки и их абон по ним плата.
            $result = $this->pointsDemand($is_face, $startDate, $endDate);
			foreach($result as $key => $value)
			{
				$totalResult[$key]  = $value;
			}
			unset($result);

            //Количество точек, ушедших в заявки в этом месяце и их абон по ним плата.
            $result = $this->currentMonthPointsDemand($is_face, $startDate, $endDate);
			foreach($result as $key => $value)
			{
				$totalResult[$key]  = $value;
			}
			unset($result);

			$totalResult['dohod'] = $totalResult['all_outcomming'] / $totalResult['count_clients'];
         	$totalResult['postupleniya'] = $totalResult['all_comming'] / $totalResult['count_clients'];

			return $totalResult;
        }
        else
        {
			// Если выбран в качестве просмотра не тек. месяц
			$totalResult = $this->selectPreStats($date, $is_face);
			return $totalResult;
        }
	}

	public function selectPreStats($date, $is_face)
	{
		$date = $date . "-01";

		$sql = "
			select * from result_report_test
			where
				client_type_id = {$is_face}
			and
				startdate = '{$date}'
		";

		$result = $this->_db->fetchAll($sql);

		foreach($result as $key => $value)
		{
			$totalResult[$value['row_name']] = $value['result'];
		}

		$totalResult['dohod'] = $totalResult['all_outcomming'] / $totalResult['count_users'];
     	$totalResult['postupleniya'] = $totalResult['all_comming'] / $totalResult['count_users'];
		$totalResult['count_clients'] = $totalResult['count_users'];
		return $totalResult;
	}


	/**
	 *  Кол. клиентов
	 *  code = "users"
	 */
	public function getAllClients($is_stream, $start, $end)
	{
		$start = $start . " 00:00:00";
		$end   = $end . " 00:00:00";


		if($is_stream == 1)
		{
			$sql = "
				SELECT
					count(distinct bind.client_id) as count_clients,
					count(bind.client_id) as count_points
				FROM
				(
					SELECT
						distinct clients.client_id
					FROM
							(select * from clients UNION ALL select * from clients_arhiv) as clients
								join
							(select * from points UNION ALL select * from points_arhiv) as points
							on (clients.client_id = points.client_id)
					WHERE
						clients.client_type_id  = {$is_stream}
					AND
						clients.client_dateagree <= '{$end}'
				) as bind, transactions as T
					where
							bind.client_id = T.client_id
						and
							T.currenttime between '{$start}' and '{$end}'
						and
							T.trantype = 1001
			";

		}
		elseif($is_stream == 0)
		{
			$sql = "
				SELECT
						count(distinct bind.client_id) as count_clients,
						count(bind.client_id) as count_points
				FROM
				(
					SELECT
						distinct clients.client_id
					FROM
						clients join points	on (clients.client_id = points.client_id)
					WHERE
						clients.client_type_id  = {$is_stream}
					AND
						clients.client_dateagree < '{$end}'
				) as bind, transactions as T
					where
							bind.client_id = T.client_id
						and
							T.currenttime between '{$start}' and '{$end}'
						and
							T.trantype = 1001
						and
							T.servicetype <> 9999
			";
		}
		$result = $this->_db->fetchAll($sql);
		return $result;
	}

	/**
	 *  Всего активных пользователей
	 *  code = "payed_clients"
	 */
	public function payedClients($is_stream, $start, $end)
	{
		$start = $start ." 00:00:00";
		$end   = date("Y-m", strtotime($start)). "-". date("t",strtotime($start)) . " 23:59:59";

		$sql = "
			SELECT
				count(distinct clients.client_id)
			FROM
				(select * from clients
					union all
				 select * from clients_arhiv) AS clients
			JOIN
				transactions ON transactions.client_id = clients.client_id
				AND
					transactions.currenttime between '{$start}' and '{$end}'
				AND
					transactions.trantype <= 100
			WHERE
				clients.client_type_id  = {$is_stream}
		";

		$result = $this->_db->fetchOne($sql);
		return $result;
	}

	/**
	 *  Вновь подключенные
	 *  code = "new_clients"
	 */
	public function newClients($is_stream, $start, $end)
	{
		$start = $start ." 00:00:00";
		$end   = date("Y-m", strtotime($start)). "-". date("t",strtotime($start)) . " 23:59:59";

		if($is_stream == 0)
		{
			$sql = "
                SELECT
                        count(CLA.client_id)
                FROM
                        (select * from clients
                        union all select * from clients_arhiv) as CLA,
                        client_services_all AS SAS
                WHERE
                        CLA.client_dateagree between '{$start}' and '{$end}'
                        AND CLA.client_type_id = {$is_stream}
                        AND CLA.client_id = SAS.client_id
			";
		}
		elseif($is_stream == 1)
		{
			$sql = "
				select
					count(CLA.client_id)
				from transactions as T, clients as CLA
				where
					T.trantype =1005
				and
					T.currenttime between '{$start}' and '{$end}'
				and
					T.client_id = CLA.client_id
				and
					CLA.client_type_id = {$is_stream}
			";
		}
		$result = $this->_db->fetchOne($sql);

		if($is_stream == 1)
        {
            $this->new_clients_stream = $result;
        }
        else
        {
            $this->new_clients_corp = $result;
        }

		return $result;
	}


    /**
     *  Отключенные:
     *  code = "deleted"
     */
    public function uncrossedClients($is_stream, $start, $end)
    {
        $sql = "
            select
                    count(PTS.u_login)
            from clients_arhiv   as CLA, points_arhiv as PTS
            where
                    CLA.client_type_id = {$is_stream}
            and
                    CLA.client_id = PTS.client_id
            and
                    to_char(PTS.last_modify_data,'YYYY-MM') = to_char('{$start}'::timestamp,'YYYY-MM')
        ";

        $result = $this->_db->fetchOne($sql);
        return $result;
    }


     /**
     *  Количество проданных модемов
     *  code = "sell_modems"
     *  code = "sell_modems_price"
     */
    public function sellModems($is_stream, $start, $end)
    {
		$start = $start ." 00:00:00";
		$end   = date("Y-m", strtotime($start)). "-". date("t",strtotime($start)) . " 23:59:59";


        $sql = "
            SELECT
                    COUNT(DISTINCT m.modem_serial) AS sell_modems,
                    SUM(m.modem_price) as sell_modems_price
            FROM
                    (SELECT * FROM clients
            		 UNION ALL
					 SELECT * FROM clients_arhiv) AS c
                    INNER JOIN modems AS m
                        ON c.client_id = m.client_id
            WHERE
                C.client_type_id = {$is_stream}
                AND C.client_dateagree between '{$start}' and '{$end}'
        ";
        $result = $this->_db->fetchRow($sql);

        if($is_stream == 1)
        {
            $this->stream_modems = $result['sell_modems'];
        }
        else
        {
            $this->corp_modems = $result['sell_modems'];
        }

        return $result;
    }


    /**
     *  Количество клиентов со своим модемом
     *  code = "without_modems"
     */
    public function withoutModems($is_stream)
    {
        if($is_stream == 1)
        {
            $result = $this->new_clients_stream - $this->stream_modems;
        }
        else
        {
            $result = $this->new_clients_corp - $this->corp_modems;
        }

        return $result;
    }


    /**
     *  Абонентская плата:
     *  code = 8 ... 34
     */
    public function getAllAbonTran($is_stream, $start, $end)
    {
            $startDate = date('Y-m-01', strtotime($start));
            $endDate = date('Y-m-t', strtotime($startDate));
            $nextMonth = date('Y-m-01', strtotime("+1 month {$startDate}"));

            $countDay    = date('t', strtotime($startDate));


            if ($is_stream == 1)
            {
                    $clientFilter = "= 1";
            }
            else
            {
                    $clientFilter = "= 0";
            }

            $clientIDsSql = "
                    SELECT
                            client_id
                    FROM
                            clients
                    WHERE
                            clients.client_type_id {$clientFilter}
                    UNION ALL (
                            SELECT
                                    client_id
                            FROM
                                    clients_arhiv
                            WHERE
                                    clients_arhiv.client_type_id {$clientFilter}
                            )
                    ";
            $clientIDData = $this->_db->fetchAll($clientIDsSql);
            $clientIDs = '';
            $comma = '';

            foreach ($clientIDData as $client)
            {
                    $clientIDs .= $comma . $client['client_id'];
                    $comma = ',';
            }

                    $sql_super_button_per_day =  "
                    SELECT
                        SUM((
                                SELECT
                                    tarif_price
                                FROM tarifs WHERE
                                    tarif_id = SAS.tarif_id
                                    AND group_name = 'dop_service'))
                    FROM stream_additional_services AS SAS
                    WHERE
                        startdate >= '{$start}' and enddate <= '{$end}'
                    ";

                    $sql_super_button_per_mon =  "
                    SELECT
                        SUM((
                                SELECT
                                    tarif_price
                                FROM
                                    tarifs
                                WHERE
                                    tarif_id = SAS.tarif_id
                                    AND group_name = 'dop_service_per_month'))
                    FROM stream_additional_services AS SAS
                    WHERE
                        startdate >= '{$start}'
                        AND enddate <= ('{$end}'::timestamp + INTERVAL '1 month')
                    ";

            $transactionsSql = "
                    SELECT
                            trantype,
                            SUM(
                                            CASE WHEN (select currency from clients where client_id = transactions.client_id) = 'UZS' and (select client_type_id from clients where client_id = transactions.client_id) = 0 THEN
                                                            sum2dollar(summa, transactions.currenttime::DATE)
                                                    ELSE
                                                            summa
                                            END
                                    )
                    FROM
                            transactions
                    WHERE
                            currenttime >= '{$startDate}'
                            AND currenttime < '{$nextMonth}'
                            AND client_id IN ({$clientIDs})
                    GROUP BY
                            trantype
            ";

            $transactionsData = $this->_db->fetchPairs($transactionsSql);

            $resultArray['abon_plata'] = $transactionsData[1001];
            $resultArray['perelimit']  = $transactionsData[1003];

            $resultArray['new_users_payed']  = $transactionsData[1005];
            $resultArray['new_users_regpay'] = $transactionsData[120];

            $resultArray['penalty'] 			 = $transactionsData[1006];
            $resultArray['activ_card'] 			 = $transactionsData[1];
            $resultArray['kassa'] 				 = $transactionsData[3];
            $resultArray['kassa_card'] 			 = $transactionsData[77];
            $resultArray['kassa_samarkand']		 = $transactionsData[40];
            $resultArray['kassa_samarkand_card'] = $transactionsData[44];
            $resultArray['paynet'] 				 = $transactionsData[9];
            $resultArray['kapital'] 			 = $transactionsData[6];
			//
            $resultArray['reg_back']			 = $transactionsData[80];
            $resultArray['zachis_bonus']		 = $transactionsData[29];
            $resultArray['zachis_peronos']		 = $transactionsData[4];
            $resultArray['zachis_aftr_pereshet'] = $transactionsData[26];
            $resultArray['kapital_corp'] 		 = $transactionsData[20];
            $resultArray['bonus'] 				 = $transactionsData[5];
            $resultArray['tp_change_zachis']	 = $transactionsData[33];
			//
            $resultArray['buh_correct_minus'] 	 = $transactionsData[7117];
            $resultArray['buh_correct_plus']	 = $transactionsData[31];

            $resultArray['dop_service_day'] 	 = $this->_db->fetchOne($sql_super_button_per_day);
            $resultArray['dop_service_mon'] 	 = $this->_db->fetchOne($sql_super_button_per_mon);


            $rezervSql = "
                    SELECT
                            SUM(tarifs.tarif_price)
                    FROM
                            adsl
                    JOIN
                            tarifs ON tarifs.tarif_id = adsl.tarif_id
                    JOIN
                            (select * from points
                            union all select * from points_arhiv) AS points
                            ON points.point_id = adsl.point_id
                    JOIN
                            (select * from clients
                            union all select * from clients_arhiv) AS clients
                            ON clients.client_id = points.client_id
                    WHERE
                            clients.client_type_id = {$is_stream}
                            AND tarifs.group_name = 'reserved'
                            AND adsl.startdate BETWEEN '{$startDate}' AND '{$endDate}'
            ";
            $resultArray['reserved'] = $this->_db->fetchOne($rezervSql);

            foreach ($transactionsData as $tranType => $amount)
            {
                    if ($tranType < 100)
                    {
                            $resultArray['all_comming'] += $amount;
                    }
                    else
                    {
                            $resultArray['all_outcomming'] += $amount;
                    }
            }

            $resultArray['all_dop_plus']  = $resultArray['dop31'] + $resultArray['dop4'];
            $resultArray['all_dop_minus'] = $resultArray['dop29'] + $resultArray['dop26'];

            $resultArray['monthly_pay'] = $resultArray['abon_plata'] +
            							  $resultArray['reserved'] +
            							  $resultArray['perelimit'] +
            							  $resultArray['penalty'] +
            							  $resultArray['dop_service_day'] +
            							  $resultArray['dop_service_mon'] +
            							  $resultArray['all_dop_minus'] +
            							  $resultArray['new_users_regpay'] +
            							  $resultArray['new_users_payed'];


        	return $resultArray;
    }



	/**
	 *  Не дополученная абон плата
	 *  code = "tran_sum"
	 *  code = "get_demand"
	 */
	 public function notReceived($is_stream, $start, $end)
	 {
		$start = $start ." 00:00:00";
		$end   = date("Y-m", strtotime($start)). "-". date("t",strtotime($start)) . " 23:59:59";


		$sql = "
            SELECT
                sum(bind2.sum) AS tran_sum,
                count(bind2.id) AS get_demand
            FROM
                (SELECT
                    *,
                    -- сумма по транзакциям
                    (SELECT
                        sum(summa)
                     FROM
                        transactions
                     WHERE
                        trantype < 100
                        AND client_id = bind1.client_id
                        AND currenttime >= '{$start}') AS sum_tran
                 FROM
                    -- получение всех транзакций со списанием абон платы
                    -- и перевод всех сумм в транзакциях в доллары
                    (SELECT
                        SUM(
                        CASE WHEN CLAS.currency = 'UZS' THEN
                            sum2dollar(T.summa::real, T.currenttime::date)
                        ELSE
                            T.summa
                        END),
                        T.client_id
                    FROM
                        transactions AS T
                        INNER JOIN clients AS CLAS
                            ON T.client_id = CLAS.client_id
                    WHERE
                        T.currenttime BETWEEN '{$start}' AND '{$end}'
                        AND T.trantype = 1001
                    GROUP BY
                        T.client_id) AS bind1
                    -- получаем информаци о списании абон платы в начале месяца
                    INNER JOIN balance_after_logs AS BAL
                        ON bind1.client_id = BAL.clientid
                    INNER JOIN
                    (SELECT client_type_id, client_id FROM clients
                     UNION ALL
                     SELECT client_type_id, client_id FROM clients_arhiv) AS CLA
                        ON BAL.clientid = CLA.client_id
                WHERE

                    BAL.cur_date = '{$start}'
                    AND BAL.ballance < 0
                    AND CLA.client_type_id = {$is_stream}) AS bind2
            WHERE
                bind2.sum_tran IS NULL
		";

		return $this->_db->fetchRow($sql);
	 }

     /**
	 *  Количество точек, ушедших в заявки и их абон по ним плата.
	 *  code = "count_demand_points"
	 *  code = "sum_demand_points"
	 */
     function pointsDemand($is_stream, $start, $end)
     {
         $sql = "
             SELECT
                COUNT(*) AS count_demand_points,
                SUM(CASE
                    WHEN c.currency = 'USD' THEN COALESCE(tp.tarif_price::real, t.tarif_price::real)
                    WHEN c.currency = 'UZS' THEN sum2dollar(COALESCE(tp.tarif_price::real, t.tarif_price::real), '{$end}')
                END) AS sum_demand_points
            FROM
                (SELECT * FROM points /*UNION ALL SELECT * FROM points_arhiv*/) p
                INNER JOIN clients c
                    ON c.client_id = p.client_id
                INNER JOIN (SELECT * FROM client_services_all
                    /*UNION ALL SELECT * FROM client_services_all_arhiv*/) AS s
                    ON p.point_id = s.point_id
                INNER JOIN tarifs t
                    ON s.tarif_id = t.tarif_id
                LEFT OUTER JOIN adsl a
                    ON p.point_id = a.point_id
                LEFT OUTER JOIN tarif_properties tp
                    ON tp.id = s.id
            WHERE
                p.dslam_id IS NOT NULL
                AND p.dslam_id != 0
                AND p.statuscross IN (-1, -2)
                AND c.client_type_id = {$is_stream}
        ";
        return $this->_db->fetchRow($sql);
     }

     /**
	 *  Количество точек, ушедших в заявки в этом месяце их абон по ним плата.
	 *  code = "count_month_demand_points"
	 *  code = "sum_month_demand_points"
	 */
     function currentMonthPointsDemand($is_stream, $start, $end)
     {
         $sql = "
            SELECT
                COUNT(*) AS count_month_demand_points,
                SUM(CASE
                    WHEN c.currency = 'USD' THEN COALESCE(tp.tarif_price::real, t.tarif_price::real)
                    WHEN c.currency = 'UZS' THEN sum2dollar(COALESCE(tp.tarif_price::real, t.tarif_price::real), '{$end}')
                END) AS sum_month_demand_points
            FROM
                log_cross_status AS lcs
                JOIN
                (SELECT
                    point_id, client_id, statuscross, dslam_id
                FROM
                    points
                UNION ALL
                SELECT
                    point_id, client_id, statuscross, dslam_id
                FROM
                    points_arhiv) AS p
                    ON lcs.point_id = p.point_id
                INNER JOIN (SELECT * FROM client_service_last
                    UNION ALL SELECT * FROM client_service_last_arhiv) AS s
                    ON p.point_id = s.point_id
                INNER JOIN tarifs t
                    ON s.tarif_id = t.tarif_id
                INNER JOIN (SELECT client_id, currency, client_type_id FROM clients UNION ALL SELECT client_id, currency, client_type_id FROM clients_arhiv) c
                    ON c.client_id = p.client_id
                LEFT OUTER JOIN adsl a
                    ON p.point_id = a.point_id
                LEFT OUTER JOIN tarif_properties tp
                    ON tp.id = s.id
            WHERE
                p.dslam_id IS NOT NULL
                AND p.dslam_id != 0
                AND p.statuscross < 0
                AND c.client_type_id = {$is_stream}
                AND lcs.actiondate BETWEEN '{$start} 00:00:00' AND '{$end} 23:59:59'
                AND lcs.status_id IN (-1, -2)
        ";

        return $this->_db->fetchRow($sql);
     }
}