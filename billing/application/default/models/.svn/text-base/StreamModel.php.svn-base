<?php
require_once('Zend/Db/Table.php');

class StreamModel extends Zend_Db_Table
{
    protected $_name = 'clients';

    public function getStaticReportData($date, $ctype)
    {
        $sql = "
            select * from result_report where client_type_id = {$ctype} and startdate = '{$date}'
			order by row_code
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getByPay($some_date, $month, $year, $is_stream)
    {
    	$startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
    	$endDate = date('Y-m-t', strtotime($startDate));
    	$nextMonth = date('Y-m-01', strtotime("+1 month {$startDate}"));

        $countDay    = date('t', strtotime($year."-".$month."-01"));
        $dataForAbon = $some_date."-01";

        $start =  $some_date."-01";
        $end =  $some_date."-".$countDay;




        if( date("m") == $month)
        {

        if ($is_stream)
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
        		SUM(summa)
        	FROM
        		transactions
        	WHERE
        		currenttime >= '{$startDate}'
        		AND currenttime < '{$nextMonth}'
        		AND client_id IN ({$clientIDs})
				AND trantype = 2101
				AND servicetype = 3000
		";

		$sql_super_button_per_mon =  "
			SELECT
        		SUM(summa)
        	FROM
        		transactions
        	WHERE
        		currenttime >= '{$startDate}'
        		AND currenttime < '{$nextMonth}'
        		AND client_id IN ({$clientIDs})
				AND trantype = 2001
				AND servicetype = 3000
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

        $resultArray['penalty'] = $transactionsData[1006];
        $resultArray['activ_card'] = $transactionsData[1];
        $resultArray['kassa'] = $transactionsData[3];
        $resultArray['kassa_card'] = $transactionsData[77];
        $resultArray['kassa_samarkand'] = $transactionsData[40];
        $resultArray['kassa_samarkand_card'] = $transactionsData[44];
        $resultArray['paynet'] = $transactionsData[9];
        $resultArray['kapital'] = $transactionsData[6];
        $resultArray['dop31'] = $transactionsData[31];
        $resultArray['dop80'] = $transactionsData[80];
        $resultArray['dop32'] = $transactionsData[32];
        $resultArray['dop29'] = $transactionsData[29];
        $resultArray['dop4'] = $transactionsData[4];
        $resultArray['dop26'] = $transactionsData[26];
        $resultArray['kapital_corp'] = $transactionsData[20];
        $resultArray['bonus'] = $transactionsData[5];
        $resultArray['dop_service_day'] = $this->_db->fetchOne($sql_super_button_per_day);
        $resultArray['dop_service_mon'] = $this->_db->fetchOne($sql_super_button_per_mon);


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

        $resultArray['monthly_pay'] = $resultArray['abon_plata'] + $resultArray['reserved'] + $resultArray['perelimit'] + $resultArray['penalty'];

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
		$resultArray['all_dop'] = $resultArray['dop31'] +  $resultArray['dop80'] + $resultArray['dop32'] + $resultArray['dop29'] + $resultArray['dop4'] + $resultArray['dop26'];


       	$usersSql = "
			SELECT
				count(bind.client_id) as count_clients
			FROM
			(
				SELECT
					distinct clients.client_id
				FROM
				clients join points
				on (clients.client_id = points.client_id)
				WHERE
					clients.client_type_id {$clientFilter}
					AND clients.client_dateagree < '{$nextMonth}'
		";

        if ($is_stream)
        {
        	$usersSql .= "
        			AND points.port_id is not null
					AND points.statuscross >= 0
					) as bind
			";
        }
        else
        {
			$usersSql .= ' ) as bind ';
        }


        $resultArray['users'] =  $this->_db->fetchOne($usersSql);

        $activeUsersSql = "
        	SELECT
        		count(bind.client_id) as count_clients
        	FROM (
        		SELECT
        			distinct clients.client_id
        		FROM
        			clients JOIN points on(clients.client_id = points.client_id)
        		LEFT JOIN
        			ports ON ports.id = points.port_id
        		WHERE
        			clients.client_type_id {$clientFilter}
        			AND clients.client_dateagree < '{$nextMonth}'
        ";

        if ($is_stream)
        {
        	$activeUsersSql .= "
        			AND points.port_id is not null
					AND points.statuscross >= 0
					AND ports.state = 1) as bind
					";
        }
        else
        {
        	$activeUsersSql .=" ) as bind";
        }


        $resultArray['active_users'] =  $this->_db->fetchOne($activeUsersSql);

        $payedUsersSql = "
            SELECT
            	count(bind2.client_id)
            FROM (
            	SELECT
					distinct clients.client_id
				FROM
					(select * from clients
	        		union all select * from clients_arhiv) AS clients
				JOIN
					transactions ON transactions.client_id = clients.client_id
					AND transactions.currenttime >= '{$startDate}'

					AND transactions.trantype <= 100
				WHERE
					clients.client_type_id {$clientFilter}
				) as bind2
        ";
        $resultArray['payed_users'] = $this->_db->fetchOne($payedUsersSql);

        $newClientsSql = "
                SELECT
                        count(CLA.client_id)
                FROM
                        (select * from clients
                        union all select * from clients_arhiv) as CLA,
                        client_services_all AS SAS
                WHERE
                        CLA.client_dateagree between '{$startDate}' and '{$endDate}'
                        AND CLA.client_type_id {$clientFilter}
                        AND CLA.client_id = SAS.client_id
        ";

        $resultArray['new_clients'] = $this->_db->fetchOne($newClientsSql);


        $sql_raskross1 = "
			SELECT
				count(clients_arhiv.client_id)
			FROM
				letters_to_ats as LTA
            JOIN
            	points_arhiv ON points_arhiv.point_id = LTA.point_id
            JOIN
            	clients_arhiv ON clients_arhiv.client_id = points_arhiv.point_id
            WHERE
				LTA.sent_date between '{$startDate}' and '{$endDate}'
				AND clients_arhiv.client_type_id {$clientFilter}
				AND LTA.kind in (2,3)
        ";

         $resultArray['rasskross'] =  $this->_db->fetchOne($sql_raskross1);


         $sql_raskross2 = "
			SELECT
				count(bind.client_type_id) from
                (SELECT
                            LTA.*,
                            (select u_login from points where point_id = LTA.point_id limit 1) as u_login,
                            (select clients.client_type_id from clients, points
                                    where
                                                clients.client_id = points.point_id
                                            and points.point_id = LTA.point_id limit 1) as client_type_id,
                            (select clients.client_name from clients, points
                                    where
                                                clients.client_id = points.point_id
                                            and points.point_id = LTA.point_id limit 1) as client_name
                    from letters_to_ats as LTA
                    where
                            sent_date between '{$startDate}' and '{$endDate}'
                    ) as bind
                    where
                            bind.client_type_id {$clientFilter}
                    and
                            bind.kind in (2,3)
        ";

         $resultArray['rasskross2'] = $this->_db->fetchOne($sql_raskross2);

         $resultArray['dohod'] = $resultArray['all_outcomming'] / $resultArray['users'];

         $resultArray['postupleniya'] = $resultArray['all_comming'] / $resultArray['users'];

         $sql_modem = "
            select
				count(C.client_id) as cm
			from
				modems as M,
				(select * from clients
	        	union all select * from clients_arhiv) as C
			where
				M.client_id = C.client_id
		    	and C.client_type_id = {$is_stream}
		    	and C.client_dateagree between '{$startDate}' and '{$endDate}'
         ";

         $resultArray['count_modems'] = $this->_db->fetchOne($sql_modem);



         $resultArray['modems_summa'] = $resultArray['count_modems'] * 40;

	 	 $resultArray['monthly_pay'] = $resultArray['monthly_pay'] + $resultArray['dop_service_mon'] + $resultArray['dop_service_day'];


	 	 $sql = "
				select sum(bind2.sum) as tran_sum, count(bind2.id) as get_demand from
				(select
					*,
					(select sum(summa) from transactions where trantype < 100 and client_id = bind1.client_id
					 and currenttime >= '{$startDate}') as sum_tran
				from
				(select sum(T.summa), T.client_id from transactions  as T
				where
					T.currenttime between '{$startDate}' and '{$endDate}'
				and
					T.trantype = 1001
				group by T.client_id) as bind1 , balance_after_logs as BAL,
					(
						select client_type_id, client_id from clients
						union all
						select client_type_id, client_id from clients_arhiv
					) as CLA
				where
					bind1.client_id = BAL.clientid
				and
					BAL.cur_date = '{$startDate}'
				and
					BAL.ballance < 0
				and
					BAL.clientid = CLA.client_id
				and
					CLA.client_type_id = {$is_stream}) as bind2
				where
					bind2.sum_tran is null
		";
		$result = $this->_db->fetchRow($sql);

		$resultArray['tran_sum'] = $result['tran_sum'];
		$resultArray['get_demand'] = $result['get_demand'];

        }
        else
        {
            $superResult = $this->getStaticReportData($nextMonth, $is_stream);
            var_dump("----TEST----");

            $resultArray['abon_plata']           = $superResult[7]['result'];
            $resultArray['perelimit']            = $superResult[8]['result'];
            $resultArray['penalty']              = $superResult[9]['result'];
            $resultArray['activ_card']           = $superResult[10]['result'];
            $resultArray['kassa']                = $superResult[11]['result'];
            $resultArray['kassa_card']           = $superResult[12]['result'];
            $resultArray['kassa_samarkand']      = $superResult[13]['result'];
            $resultArray['kassa_samarkand_card'] = $superResult[14]['result'];
            $resultArray['paynet']               = $superResult[15]['result'];
            $resultArray['kapital']              = $superResult[16]['result'];
            $resultArray['dop31']                = $superResult[17]['result'];
            $resultArray['dop80']                = $superResult[18]['result'];
            $resultArray['dop32']                = $superResult[19]['result'];
            $resultArray['dop29']                = $superResult[20]['result'];
            $resultArray['dop4']                 = $superResult[21]['result'];
            $resultArray['dop26']                = $superResult[22]['result'];
            $resultArray['kapital_corp']         = $superResult[23]['result'];
            $resultArray['bonus']                = $superResult[24]['result'];
            $resultArray['dop_service_day']      = $superResult[25]['result'];
            $resultArray['dop_service_mon']      = $superResult[26]['result'];
            $resultArray['reserved']             = $superResult[27]['result'];
            $resultArray['monthly_pay']          = $superResult[28]['result'];
            $resultArray['all_comming']          = $superResult[29]['result'];
            $resultArray['all_outcomming']       = $superResult[30]['result'];
            $resultArray['all_dop']              = $superResult[31]['result'];
            $resultArray['new_users_payed']        = $superResult[32]['result'];
            $resultArray['new_users_regpay']       = $superResult[33]['result'];
            $resultArray['tran_sum']       		   = $superResult[34]['result'];
            $resultArray['get_demand']       	   = $superResult[35]['result'];


            $resultArray['users']        = $superResult[0]['result'];
            $resultArray['active_users'] = $superResult[1]['result'];
            $resultArray['payed_users']  = $superResult[2]['result'];
            $resultArray['new_clients']  = $superResult[3]['result'];
            $resultArray['rasskross']    = $superResult[4]['result'];

            $resultArray['dohod']        = $superResult[30]['result'] / $superResult[0]['result'];
            $resultArray['postupleniya'] = $superResult[29]['result'] / $superResult[0]['result'];

            $resultArray['count_modems']    = $superResult[5]['result'];

        }
         return $resultArray;

    }




    public function getByPayTest($some_date, $month, $year, $is_stream)
    {

        $startDate = date('Y-m-01', strtotime("{$year}-{$month}-01")). " 00:00:00";
    	$endDate = date('Y-m-t', strtotime($startDate)). " 23:59:59";

        $sql_trans = "
            select
                    T.trantype,
                    sum(T.summa) as summa,
                    AST.typename
            from transactions as T, clients as CLA, asrtypes as AST
            where
                    T.currenttime between '{$startDate}' and '{$endDate}'
            and
                    T.client_id = CLA.client_id
            and
                    CLA.client_type_id = {$is_stream}
            and
                    T.trantype = AST.typename_id
            and
                    AST.typename_value = '13'
            group by T.trantype, AST.typename
            order by T.trantype
        ";

        $data_trans = $this->_db->fetchAll($sql_trans);

        $arrSumm     = array();
        $arrDecrease = array();

        for($i = 0; $i < count($data_trans); $i++)
        {
              if($data_trans[$i]['trantype'] < 100)
              {
                  array_push($arrSumm, $data_trans[$i]);
              }
              else
              {
                  array_push($arrDecrease, $data_trans[$i]);
              }
        }
        $total['decrease']  = $arrDecrease;
        $total['transfer'] = $arrSumm;

        return $total;
    }

    public function getByPorts($some_date, $month, $year)
    {
        $countDay    = date('t', strtotime($year."-".$month."-01"));
        $dataForAbon = $some_date."-01";

        $start =  $some_date."-01";
        $end =  $some_date."-".$countDay;

        $count_ports = "
            select sum(count_ports) as cp from
			 (select
				D.*,
				(select count(id) from ports where dslam_id = D.id) as count_ports
			 from dslam_list  as D
			 where
				D.is_deleted = false) as bind
        ";

        $result['count_ports'] = $this->_db->fetchOne($count_ports);


        $ojid_rasskross = "
            select count(point_id) from
			(select
				PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype
			from points as PTS
			where
				PTS.statuscross in (-1, -2, -3)) as bind
			where
				bind.ctype = 1
        ";
        $result['ojid_rasskross'] = $this->_db->fetchOne($ojid_rasskross);


        $ojid_crossa = "
            select count(point_id) from
			(select
				PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype
			from points as PTS
			where
				PTS.statuscross in (1, 2, 3)) as bind
			where
				bind.ctype = 1
        ";
        $result['ojid_kross'] = $this->_db->fetchOne($ojid_crossa);

        $cross_done = "
			select count(point_id) from
			(select
				PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype
			from points as PTS
			where
				PTS.statuscross in (25)) as bind
			where
				bind.ctype = 1
        ";
        $result['cross_done'] = $this->_db->fetchOne($cross_done);

        $sql_upports = "
			select count(point_id) from
			(select
				PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype,
				(select state from ports where id = PTS.port_id) as port_state
			from points as PTS
			) as bind
			where
				bind.ctype = 1
			and
				bind.port_state = 1
        ";
        $result['upports'] = $this->_db->fetchOne($sql_upports);

        $sql_downports = "
			select count(point_id) from
			(select
				PTS.*,
				(select client_type_id from clients where client_id = PTS.client_id limit 1) as ctype,
				(select state from ports where id = PTS.port_id) as port_state
			from points as PTS
			) as bind
			where
				bind.ctype = 1
			and
				bind.port_state = 0
        ";
        $result['downports'] = $this->_db->fetchOne($sql_downports);

        return $result;
    }

    public function getByClient($some_date, $month, $year, $is_stream)
    {
    	$some_date = $some_date. "-01";

		//Количество клиентов SharqStream, с которых снята абон. плата за месяц
		$sql_transac = "
				SELECT
					DISTINCT T.client_id
				FROM
					transactions as T,
					(select * from  clients union all select * from clients_arhiv) as CLA
				WHERE
					T.trantype = 1001
					and	to_char(T.currenttime::timestamp, 'YYYY-MM') = to_char('{$some_date}'::timestamp, 'YYYY-MM')
					and T.client_id = CLA.client_id
					and CLA.client_type_id = {$is_stream}
				GROUP BY
					T.client_id
		";

		$selectData = $this->_db->fetchCol($sql_transac);

		$result['count_abon_payments']  = count($selectData);

		$sql_transac = "
				SELECT
					DISTINCT T.client_id
				FROM
					transactions as T,
					(select * from  clients union all select * from clients_arhiv) as CLA
				WHERE
					T.trantype = 1005
					and	to_char(T.currenttime::timestamp, 'YYYY-MM') = to_char('{$some_date}'::timestamp, 'YYYY-MM')
					and T.client_id = CLA.client_id
					and CLA.client_type_id = {$is_stream}
					AND T.client_id NOT IN (" . implode(',', $selectData) . ")
				GROUP BY
					T.client_id
		";

		$selectAllData = $this->_db->fetchCol($sql_transac);

		$result['count_all_payments']  = count($selectAllData) + $result['count_abon_payments'];

		$sql_transac = "
				SELECT
					DISTINCT clients.client_id
				FROM
					clients
				JOIN
					points on points.client_id = clients.client_id
				WHERE
					clients.ballance > 0
					AND clients.client_type_id = {$is_stream}
					AND points.statuscross >= 0
				GROUP BY
					clients.client_id
		";

		$selectAllData = $this->_db->fetchCol($sql_transac);

		$result['now_positive_clients'] = count($selectAllData);

		//Количество клиентов SharqStream с положительным балансом на 28.02
		$sql_plus = "
			select
				count(BL.clientid) as counter
			from
				ballance_logs  as BL,
				(select * from  clients union all select * from clients_arhiv) as CLA
			where
				BL.clientid = CLA.client_id
				AND CLA.client_type_id = {$is_stream}
				AND BL.ballance >= 0
				AND Bl.cur_date =  '{$some_date}'
				AND CLA.client_id IN (" . implode(',', $selectData) . ")
		";

		$result['clients_plus'] = $this->_db->fetchOne($sql_plus);

		//Количество клиентов SharqStream с отриц балансом на 28.02
		$sql_minus = "
			select
				count(BL.clientid) as counter
			from
				ballance_logs  as BL,
				(select * from  clients union all select * from clients_arhiv) as CLA
			where
				BL.clientid = CLA.client_id
				and CLA.client_type_id = {$is_stream}
				and BL.ballance <= -0.01
				and Bl.cur_date =  '{$some_date}'
				AND CLA.client_id IN (" . implode(',', $selectData) . ")
		";
		$result['clients_minus'] = $this->_db->fetchOne($sql_minus);

		return $result;
    }

	public function getDialup($date)
	{
		$start = $date."-01";
		$end = date('Y-m-t', strtotime($start));

		$sql_smile_clients = "
			select sum(summa), bind.client_id from
			(select
				CLA.client_id,
				(select sum(summa) from transactions where client_id = CLA.client_id and trantype in(1003, 110)) as summa
			from clients as CLA
			where
				CLA.client_dateagree between '{$start}' and '{$end}'
			and
				CLA.client_type_id = 3) as bind
			group by bind.client_id
		";

		$sql_dialup_clients = "
			select sum(summa), bind.client_id from
			(select
				CLA.client_id,
				(select sum(summa) from transactions where client_id = CLA.client_id and trantype in(1003, 110)) as summa
			from clients as CLA
			where
				CLA.client_dateagree between '{$start}' and '{$end}'
			and
				CLA.client_type_id = 2) as bind
			group by bind.client_id
		";
		// 110 Снятие абон платы
		// 1003 Перелимит

		$dialup_clients = $this->_db->fetchAll($sql_dialup_clients);
		$smile_clients  = $this->_db->fetchAll($sql_smile_clients);

		$amountDialup = 0;
		$amountSmile  = 0;

		foreach($dialup_clients as $line)
		{
			if($line['sum'])
			{
				$amountDialup = $amountDialup + $line['sum'];
			}

		}

		foreach($smile_clients as $line)
		{
			if($line['sum'])
			{
				$amountSmile = $amountSmile + $line['sum'];
			}

		}
		$arr['dialup']['count'] = count($dialup_clients);
		$arr['dialup']['amount'] = $amountDialup;

		$arr['smile']['count'] = count($smile_clients);
		$arr['smile']['amount'] = $amountSmile;

		$arr['total'] = $amountDialup + $amountSmile;
		return $arr;
	}


    public function getCorpPhoneService($date, $face)
    {
		$start = $date."-01";
		$end = date('Y-m-t', strtotime($start));


    	$servicesCode = array(
    						'NGN' 	   => 5000,
    						'PRI ISDN' => 7030,
    						'Trad TEL' => 7060,
							'IVR' 	   => 8020,
							'PIN'      => 8060
    					);


		if($face == 1)
		{
			unset($servicesCode);
			$servicesCode = array('NGN' => 5000);
		}

		$inline_services = implode(', ', $servicesCode);
		$sql_pere = "
			select
					sum(T.summa) as summa,
					T.servicetype,
					(select servicetype_name from service_type where servicetype_id = T.servicetype) as service_name
			from transactions as T, clients as CLA
			where
				CLA.client_id = T.client_id
			and
				CLA.client_type_id = {$face}
			and
				T.trantype = 7122
			and
				T.currenttime between '{$start}' and '{$end}'
			group by T.servicetype
		";
		$peregovor_phone = $this->_db->fetchAll($sql_pere);

		$sql_abon = "
			select
					sum(T.summa) as summa,
					T.servicetype,
					count(T.client_id) as count_clients,
					(select servicetype_name from service_type where servicetype_id = T.servicetype) as service_name
			from transactions  as T, clients as CLA
			where
				CLA.client_id = T.client_id
			and
				CLA.client_type_id = {$face}
			and
				T.trantype = 1001
			and
				T.currenttime between '{$start}' and '{$end}'
			and
				T.servicetype in ({$inline_services})
			group by T.servicetype
		";
		$abon_phone = $this->_db->fetchAll($sql_abon);

		$arr['peregovor'] = $peregovor_phone;
		$arr['abon'] = $abon_phone;

		return $arr;
    }

}
