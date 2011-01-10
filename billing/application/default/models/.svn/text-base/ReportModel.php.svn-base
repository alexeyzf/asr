<?php
require_once('Zend/Db/Table.php');

class ReportModel extends Zend_Db_Table
{

    public function getAtsIDs()
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
            select * from ats_list where hub_id={$hub_id} order by name
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getPortsReport()
    {
        $sql = "
            select
                    PHL.id as hub_id,
                    PHL.name as hub_name,
                    AL.name as ats_name,
                    (select count(dslam_list.id) from dslam_list, ats_list
                                    where
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id) as count_dslams,
                    (select count(ports.id) from ports, dslam_list, ats_list
                                    where
                                            ports.dslam_id = dslam_list.id
                                    and
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id)as hub_count_ports,
                    (select count(ports.id) from ports, dslam_list, ats_list
                                    where
                                            ports.dslam_id = dslam_list.id
                                    and
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id
                                    and
                                            ports.state = 1)as count_switch_on_ports,
                    (select count(ports.id) from ports, dslam_list, ats_list
                                    where
                                            ports.dslam_id = dslam_list.id
                                    and
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id
                                    and
                                            ports.state = 0)as count_switch_off_ports,

                    (select count(ports.id) from ports, dslam_list, ats_list
                                    where
                                            ports.dslam_id = dslam_list.id
                                    and
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id
                                    and
                                            ports.status = 0)as count_status_empty,

                    (select count(ports.id) from ports, dslam_list, ats_list
                                    where
                                            ports.dslam_id = dslam_list.id
                                    and
                                            dslam_list.ats_id = ats_list.id
                                    and
                                            ats_list.phone_hub_id = PHL.id
                                    and
                                            ports.status = -1)as count_status_status_not_correct,
                    DL.*,
                    (select a_value from asrtypes where typename_value = '5' and typename_id = DL.type_id) as count_ports,
                    (select count(id) from ports where status = 0  and ports.dslam_id = DL.id ) as  status_empty,
                    (select count(id) from ports where status = 1  and ports.dslam_id = DL.id ) as  status_full,
                    (select count(id) from ports where status = -1  and ports.dslam_id = DL.id ) as  status_not_correct,
                    (select count(id) from ports where state = 1  and ports.dslam_id = DL.id ) as  switch_on,
                    (select count(id) from ports where state = 0  and ports.dslam_id = DL.id ) as  switch_off
            from ats_list as AL, dslam_list as DL, phone_hub_list as PHL
            where
                    AL.id = DL.ats_id
            and
                    AL.phone_hub_id = PHL.id
			and
					DL.is_deleted = false
            order by hub_id, ats_id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getReport($report_type)
    {
    	switch ($report_type)
    	{
			case '0_s':
					$query = "
					select
					    CLA.client_id,
						CLA.ballance,
						CLA.client_name,
						'Клиенты с положительным балансом, рабочих' as title

					from
					    clients as CLA
					where
						CLA.ballance > 0
					and
						CLA.client_type_id = 1
					order by CLA.client_name
					";
			break;

			case '1_s':
					$query = "
					select
					distinct
					    CLA.client_id,
						CLA.ballance,
						CLA.client_name,
						PTS.u_login,
						PTS.pcross,
						PTS.point_id,
						'Клиенты ожидающие кроссировки' as title

					from
					    clients as CLA, points as PTS
					where
						PTS.statuscross = 6
					and
						PTS.client_id = CLA.client_id
					and
						CLA.client_type_id = 1
					order by CLA.client_name
					";
			break;

			case '2_s':
					$query = "
					select
					distinct
					    CLA.client_id,
						CLA.ballance,
						CLA.client_name,
						PTS.u_login,
						PTS.pcross,
						PTS.point_id,
						'Клиенты ожидающие расскроса' as title

					from
					    clients as CLA, points as PTS
					where
						PTS.statuscross = -1
					and
						PTS.client_id = CLA.client_id
					and
						CLA.client_type_id = 1
					order by CLA.client_name
					";
			break;

			case '3_s':
					$query = "
							select
							BINS.* ,
							CLA.*,
							PTS.point_id,
							'Клиенты c отрицательным балансом и выключенными услугами' as title

							from ballance_in_passiv_and_service_off as BINS, clients as CLA
							where
								BINS.client_id = CLA.client_id
							and
								BINS.penable = false
							and
								CLA.ballance <= -0.01
							and
								CLA.client_type_id = 1
					";
			break;

			case '0_c':
					$query = "
							select
							BINS.* ,
							CLA.*,
							'Клиенты с положительным балансом и рабочими услугами' as title

							from ballance_in_passiv_and_service_off as BINS, clients as CLA
							where
								BINS.client_id = CLA.client_id
							and
								BINS.penable = true
							and
								CLA.ballance > 0
							and
								CLA.client_type_id = 0
					";
			break;

			case '1_c':
				$query = "
					select
						CLA.client_id,
						CLA.client_name,
						CLA.ballance,
						PTS.point_id,
						PTS.u_login,
						PTS.port_id,
						PTS.pcross,
						'Клиенты c отрицательным балансом и опущенным портом' as title

					from points as PTS, clients as CLA, ports as PORT
					where
						PTS.client_id = CLA.client_id
					and
						PTS.port_id = PORT.id
					and
						PORT.state = 0
					and
						CLA.ballance <= -0.01
					and
						CLA.client_type_id = 0
				";
			break;

			case '2_c':
				$query = "
					select
						CLA.client_id,
						CLA.client_name,
						CLA.ballance,
						'Клиенты c услугой \"Овердрафт\"' as title,
						CLA.overdraft

					from clients as CLA
					where
						CLA.client_type_id = 0
					and
						CLA.overdraft > 0
					group by CLA.client_id, CLA.client_name, CLA.overdraft, CLA.ballance
				";
			break;

			case '0_p':
				$query = "
					select
						PTS.*,
						CLA.*,
						PTS.point_id,
						'Точки ожидающие кросса ' as title
					from points as PTS, clients as CLA
					where
						PTS.client_id = CLA.client_id
					and
						PTS.statuscross = 1
				";
			break;

			case '1_p':
				$query = "
					select
						PTS.*,
						CLA.*,
						PTS.point_id,
						'Точки ожидающие расскроса ' as title
					from points as PTS, clients as CLA
					where
						PTS.client_id = CLA.client_id
					and
						PTS.statuscross = -1
				";
			break;

			case '2_p':
				$query = "
				select
					PTS.*,
					PRT.*,
					CLA.*,
					PTS.point_id,
					'Точки с поднятым портом ' as title
				from points as PTS , ports as PRT, clients as CLA
				where
					CLA.client_id = PTS.client_id
				and
					PTS.port_id = PRT.id
				and
					PRT.state = 1
				";
			break;

			case '3_p':
				$query = "
				select
					PTS.*,
					PRT.*,
					CLA.*,
					PTS.point_id,
					'Точки с опущенным портом ' as title
				from points as PTS , ports as PRT, clients as CLA
				where
					CLA.client_id = PTS.client_id
				and
					PTS.port_id = PRT.id
				and
					PRT.state = 0
				";
			break;
		}

			return $this->_db->fetchAll($query);
  }

 	public function getReestrByService($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				servicename,
			    (
				CASE WHEN clients.currency = 'UZS' THEN
					sum2dollar(sum(invd.total)::real, '{$endDate}')
				ELSE
					sum(invd.total)
				END
			   	)::double precision AS amount_usd,

 				sum(dollar2sum(invd.total::real, '{$endDate}'))::double precision AS amount_uzs,
 				count(distinct inv.client_id) AS clients_count,
 				count(distinct invd.point_id) As points_count
			FROM
				invoice_details invd
			JOIN
				invoices inv on inv.invoice_id = invd.invoice_id
			LEFT JOIN
				(select * from clients
 				union all select * from clients_arhiv ) AS clients
 				ON  clients.client_id = inv.client_id
			WHERE
				inv.lastdate between '{$startDate}' AND '{$endDate}'
				AND invd.total != 0
				AND clients.vip = false
 				AND clients.is_employee = false
 				AND clients.is_donate = false
 			GROUP BY
 				servicename,
				clients.currency
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	public function getReestrByServiceAndMonth($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				service_type.short_name AS service,
 				service_type.tablelink,
 				registry.service_type,
 				date_trunc('month', registry.last_date) AS month,
 				sum(amount)::double precision AS amount_usd,
 				sum(amount_uzs)::double precision AS amount_uzs,
 				avg(clients_count) AS clients_count,
 				avg(ports_count) AS ports_count,
 				avg(points_count) AS points_count
 			FROM
 				registry
 			LEFT JOIN
 				service_type ON service_type.servicetype_id = registry.service_type
 			WHERE
 				registry.last_date between '{$startDate}' AND '{$endDate}'
 			GROUP BY
 				service_type.short_name,
 				service_type.tablelink,
 				registry.service_type,
 				date_trunc('month', registry.last_date)
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	public function getReestrByClient($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				clients.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				clients.address,
 				clients.phone,
 				sum(invoice_details.total)::double precision AS amount,
 				sum(invoice_details.total_uzs)::double precision AS amount_uzs
 			FROM
 				invoices
 			LEFT JOIN
 				(select * from clients
 				union all select * from clients_arhiv ) AS clients
 				ON  clients.client_id = invoices.client_id
 			JOIN
 				invoice_details ON invoice_details.invoice_id = invoices.invoice_id
 			WHERE
 				invoices.lastdate between '{$startDate}' AND '{$endDate}'
 				AND invoice_details.total != 0
 				AND clients.vip = false
 				AND clients.is_employee = false
 				AND clients.is_donate = false
 			GROUP BY
 				clients.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				clients.address,
 				clients.phone
 			ORDER BY clients.client_id
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	public function getReestrByServiceNClient($startDate, $endDate, $orderBy = 'client_name')
 	{
 		$sql = "
 			SELECT
 				invoices.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				invoice_details.servicename,
 				clients.address,
 				clients.phone,
				clients.currency,
			   (
				CASE WHEN clients.currency = 'UZS' THEN
					sum2dollar(sum(invoice_details.total)::real, '{$endDate}')
				ELSE
					sum(invoice_details.total)
				END
			   )::double precision AS amount,
			   (
				CASE WHEN clients.currency = 'UZS' THEN
					dollar2sum(sum2dollar(sum(invoice_details.total)::real, '{$endDate}'))
				ELSE
					sum(invoice_details.total_uzs)
				END
			   )::double precision AS amount_uzs
 			FROM
 				invoices
 			LEFT JOIN
 				(select * from clients
 				union all select * from clients_arhiv ) AS clients
 				ON clients.client_id = invoices.client_id
 			JOIN
 				invoice_details ON invoice_details.invoice_id = invoices.invoice_id
 			WHERE
 				invoices.lastdate between '{$startDate}' AND '{$endDate}'
 				AND invoice_details.total != 0
 				AND clients.vip = false
 				AND clients.is_employee = false
 				AND clients.is_donate = false
 			GROUP BY
 				invoices.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				invoice_details.servicename,
 				clients.address,
 				clients.phone,
				clients.currency
 			ORDER BY {$orderBy}
 		";

 		return $this->_db->fetchAll($sql);
 	}

    public function getUsedTransactionsType($startDate, $finishDate, $clientType)
    {
        $sql = "
            SELECT DISTINCT
                asrtypes.typename, transactions.trantype
            FROM
                transactions
                INNER JOIN asrtypes
                    ON asrtypes.typename_id = transactions.trantype
                    AND asrtypes.typename_value = '13'
                INNER JOIN
                    (SELECT
                        client_type_id, client_id
                     FROM
                        clients
                     UNION ALL
                     SELECT
                        client_type_id, client_id
                     FROM
                        clients_arhiv) AS clients
                        ON clients.client_id = transactions.client_id
            WHERE
                trantype < 100
                AND currenttime BETWEEN '{$startDate} 00:00:00' AND '{$finishDate} 23:59:59'
                AND clients.client_type_id = {$clientType}
        ";
        return $this->_db->fetchAll($sql);
    }

 	public function getIncomesByTypes($startDate, $endDate, $clientType = null)
 	{
		$startDate = $startDate. " 00:00:00";
		$endDate   = $endDate. " 23:59:59";

 		$sql = "
 			SELECT
 				transactions.trantype,
 				transactions.userid,
 				(
					CASE WHEN clients.currency = 'UZS' and (clients.client_type_id = 0 or clients.client_type_id = 4) THEN
							sum2dollar(summa, transactions.currenttime::DATE)
						ELSE
							summa
					END
				) AS amount_usd,
 				summas AS amount_uzs
 			FROM
 				transactions
 			INNER JOIN
 				(SELECT client_id, client_type_id, currency
                 FROM
                    clients
                 UNION ALL
                 SELECT client_id, client_type_id, currency
                 FROM
                    clients_arhiv) AS clients ON clients.client_id = transactions.client_id
 			WHERE
 				trantype < 100
 				AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'
 		";

 		if ( isset($clientType) )
 		{
 			$sql .= " AND clients.client_type_id = {$clientType} ";
 		}

 		$sql .= " ORDER BY trantype ";

 		$data = $this->_db->fetchAll($sql);

 		$asrSql = "
 			SELECT
 				typename_id,
 				typename
 			FROM
 				asrtypes
 			WHERE
 				typename_value = '13'
 		";

 		$tranTypes = $this->_db->fetchPairs($asrSql);

 		$listAmounts = array();

 		foreach ($data as $row)
 		{
 			$listAmounts[ $tranTypes[ $row['trantype'] ] ]['amount_usd'] = $listAmounts[ $tranTypes[ $row['trantype'] ] ]['amount_usd'] + $row['amount_usd'];
 			$listAmounts[ $tranTypes[ $row['trantype'] ] ]['amount_uzs'] = $listAmounts[ $tranTypes[ $row['trantype'] ] ]['amount_uzs'] + $row['amount_uzs'];
 			$listAmounts[ $tranTypes[ $row['trantype'] ] ]['trantype'] = $row['trantype'];
 		}

 		return $listAmounts;
 	}

 	public function getIncomesByTypesAndMonths($startDate, $endDate, $clientType = null)
 	{
 		$startDate = $startDate. " 00:00:00";
		$endDate   = $endDate. " 23:59:59";

 		$sql = "
 			SELECT
 				transactions.trantype,
 				asrtypes.typename AS trantype_name,
 				date_trunc('month', transactions.currenttime) AS month,
 				SUM(summa)::double precision AS amount_usd
 			FROM
 				transactions
 			LEFT JOIN
 				(select * from clients
 				union all select * from clients_arhiv) as clients
 				ON clients.client_id = transactions.client_id
 			LEFT JOIN
 				asrtypes ON asrtypes.typename_id = transactions.trantype
 				AND asrtypes.typename_value = '13'
 			WHERE
 				trantype < 100
 				AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'

 		";

 		if ( isset($clientType) )
 		{
 			$sql .= " AND clients.client_type_id = {$clientType} ";
 		}

 		$sql .= "
 			GROUP BY
 				transactions.trantype,
 				asrtypes.typename,
 				date_trunc('month', transactions.currenttime)
 			ORDER BY trantype
 		";

 		$data = $this->_db->fetchAll($sql);

 		return $data;
 	}

 	public function getIncomesByClients($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				clients.client_id,
 				clients.client_name,
				sum(
 				(
					CASE WHEN clients.currency = 'UZS' and (clients.client_type_id = 0 or clients.client_type_id = 4) THEN
							sum2dollar(summa, transactions.currenttime::DATE)
						ELSE
							summa
					END
				)) as amount_usd,
 				sum(summas) AS amount_uzs
 			FROM
 				transactions
 			LEFT JOIN
 				clients ON clients.client_id = transactions.client_id
 			WHERE
 				trantype < 100
 				AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'
 			GROUP BY
 				clients.client_id,
 				clients.client_name
 			ORDER BY
 				clients.client_name
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	public function getIncomesByPaymentSystems($startDate, $endDate, $trantype, $userID)
 	{
 		$sql = "
 			SELECT
 				clients.client_id,
 				clients.client_name,
				(select u_login from points where client_id = clients.client_id limit 1) as u_login,
 				sum(summa) AS amount_usd,
 				sum(summas) AS amount_uzs
 			FROM
 				transactions
 			LEFT JOIN
 				clients ON clients.client_id = transactions.client_id
 			WHERE
 				trantype = {$trantype} and userid={$userID}
 				AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'
 			GROUP BY
 				clients.client_id,
 				clients.client_name
 			ORDER BY
 				clients.client_name
 		";

 		return $this->_db->fetchAll($sql);
 	}

        public function getIncomesByPaymentCardsKapital($startDate, $endDate, $trantype)
 	{
 		$sql = "
 			SELECT
 				clients.client_id,
 				clients.client_name,
				(select u_login from points where client_id = clients.client_id limit 1) as u_login,
 				sum(summa) AS amount_usd,
 				sum(summas) AS amount_uzs
 			FROM
 				transactions
 			LEFT JOIN
 				clients ON clients.client_id = transactions.client_id
 			WHERE
 				trantype = {$trantype}
 			AND
                                currenttime BETWEEN '{$startDate}' AND '{$endDate}'

 			GROUP BY
 				clients.client_id,
 				clients.client_name
 			ORDER BY
 				clients.client_name
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	public function getIncomesByServices($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				transactions.servicetype,
 				sum(
					(
						CASE WHEN clients.currency = 'UZS' and (clients.client_type_id = 0 or clients.client_type_id = 4) THEN
								sum2dollar(summa, transactions.currenttime::DATE)
							ELSE
								summa
						END
					)
				) AS amount_usd,
 				sum(summas) AS amount_uzs
 			FROM
 				transactions, clients
 			WHERE
 					trantype > 100
 				AND
					currenttime BETWEEN '{$startDate}' AND '{$endDate}'
				AND
					transactions.client_id = clients.client_id
 			GROUP BY
 				transactions.servicetype
 			ORDER BY
 				transactions.servicetype
 		";

 		$serviceTypeSql = "
 			SELECT
 				servicetype_id,
 				short_name
 			FROM
 				service_type
 		";

 		$transactions = $this->_db->fetchAll($sql);
 		$serviceTypes = $this->_db->fetchPairs($serviceTypeSql);

 		foreach ($transactions as $key => $transaction)
 		{
 			if ($serviceTypes[$transaction['servicetype']])
 			{
 				$transactions[$key]['servicetype'] = $serviceTypes[$transaction['servicetype']];
 			}
 		}

 		return $transactions;
 	}

 	public function getIncomesDetails($type, $startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				transactions.currenttime,
 				clients.client_id,
 				clients.client_name,
 				summa AS amount_usd,
 				summas AS amount_uzs,
 				users.first_name,
 				users.last_name,
 				transactions.commente
 			FROM
 				transactions
 			LEFT JOIN
 				clients ON clients.client_id = transactions.client_id
 			LEFT JOIN
 				users ON users.id = transactions.userid
 			WHERE
 				trantype = {$type}
 				AND currenttime BETWEEN '{$startDate}' AND '{$endDate}'
 			ORDER BY
 				transactions.currenttime
 		";

 		return $this->_db->fetchAll($sql);
 	}

 	private $_phoneTarifs;

 	public function calulateCalls($calls, $tarifs, $detailsBy = 'directions')
 	{
 		$result = array();

 		$tarifCount = count($tarifs);

 		foreach ($calls as $call)
 		{
 			$phone = $call['abonent2'];
 			$phoneLen = strlen($phone);

 			if (substr($phone, 0, 3) != '859')
 			{
 				if ( substr($phone, 0, 3) != '810' )
 				{
 					$startNumbers = substr($phone, 0, 5);
 				}
 				else
 				{
 					$startNumbers = substr($phone, 0, $phoneLen - 6);
 				}
 			}
 			else
 			{
 				$startNumbers = substr($phone, 0, $phoneLen - 3);
 			}

 			if ($this->_phoneTarifs[$startNumbers])
 			{
 				$tarif = $this->_phoneTarifs[$startNumbers];

 				if ($detailsBy == 'directions'
 					|| $detailsBy == 'ut_name')
 				{
 					$key = $tarif[$detailsBy];
 				}
 				else
 				{
 					$key = $call[$detailsBy];
 				}

 				if ( ! $result[$key] )
 				{
 					$result[$key] = array(
 						'minutes'	=> 0,
 						'amount'	=> 0,
 						'price'		=> 0
 					);
 				}

 				$result[$key]['minutes'] += $call['minutes_count'];
 				$result[$key]['amount'] += $call['minutes_count'] * $tarif['price'];
 				$result[$key]['price'] = $tarif['price'];
 				$result[$key]['is_inside_country'] = $tarif['is_inside_country'];
 			}
 			else
 			{
	 			foreach ($tarifs as $tarif)
	 			{
	 				$prefix = $tarif['prefix'];
	 				$prefixLen = strlen($prefix);

	 				if ($phoneLen < $prefixLen + 3)
	 				{
	 					continue;
	 				}

	 				if (substr($phone, 0, $prefixLen) == $prefix
	 					&& $call['call_date'] >= $tarif['start_date']
	 					&& $call['call_date'] <= $tarif['end_date'])
	 				{
	 					$this->_phoneTarifs[$startNumbers] = $tarif;

	 					if ( ! $tarif['price'] )
	 					{
	 						break;
	 					}

	 					if ($detailsBy == 'directions'
	 						|| $detailsBy == 'ut_name')
	 					{
	 						$key = $tarif[$detailsBy];
	 					}
	 					else
	 					{
	 						$key = $call[$detailsBy];
	 					}

	 					if ( ! $result[$key] )
	 					{
	 						$result[$key] = array(
	 							'minutes'	=> 0,
	 							'amount'	=> 0,
	 							'price'		=> 0
	 						);
	 					}

	 					$result[$key]['minutes'] += $call['minutes_count'];
	 					$result[$key]['amount'] += $call['minutes_count'] * $tarif['price'];
	 					$result[$key]['price'] = $tarif['price'];
	 					$result[$key]['is_inside_country'] = $tarif['is_inside_country'];
						break;
	 				}
	 			}
 			}

 		}
 		ksort($result);

 		return $result;
 	}

 	public function getReportOnInclusions($start, $end, $is_stream)
 	{
		$sql = "
            SELECT DISTINCT
                c.client_id, s.id, s.point_id, s.tablename, t.tarif_id,  c.client_name, c.client_dateagree, c.phone,
                CASE
                    WHEN t.speed = '0/0' THEN tp.speed_down || '/' || tp.speed_up
                    ELSE t.speed
                END AS speed,
                CASE
                    WHEN c.currency = 'USD' THEN c.ballance
                    WHEN c.currency = 'UZS' THEN sum2dollar(c.ballance::real, '{$end}')
                END AS ballance,
                CASE
                    WHEN c.currency = 'USD' THEN s.reg_pay
                    WHEN c.currency = 'UZS' THEN sum2dollar(s.reg_pay::real, '{$end}')
                END AS reg_pay,
                get_real_abon_price(t.tarif_price, tp.tarif_price, c.currency, c.client_dateagree) as tarif_price,
                at.typename AS country, al.name AS ats_name, phl.name AS hub_name,
                t.limit, m.modem_price, m.modem_serial, u.first_name || ' ' || u.last_name AS manager, s.startdate, s.discount
            FROM
                (SELECT * FROM clients UNION ALL SELECT * from clients_arhiv) c
                LEFT JOIN
                    (SELECT * FROM client_services_all UNION ALL SELECT * FROM client_services_all) s
                    ON s.client_id = c.client_id
                LEFT JOIN contracts contr
                    ON c.client_id = contr.client_id
                LEFT JOIN users u
                    on contr.manager_id = u.id
                LEFT JOIN tarifs t
                    ON s.tarif_id = t.tarif_id
                LEFT JOIN asrtypes at
                    ON at.typename_value = '1'
                    AND at.typename_id = s.country_id
                LEFT JOIN ats_list al
                    ON al.id = s.ats_id
                LEFT JOIN phone_hub_list phl
                    ON phl.id = al.phone_hub_id
                LEFT JOIN adsl a
                    ON s.point_id = a.point_id
                LEFT JOIN tarif_properties tp
                    ON a.id = tp.service_id
                LEFT JOIN modems m
                    ON m.point_id = s.point_id
            WHERE
                c.client_dateagree BETWEEN '{$start}' AND '{$end}'
                AND c.client_type_id = {$is_stream}
            ORDER BY c.client_dateagree
		";

		return $this->_db->fetchAll($sql);
 	}

    public function getInclusionsSummaryReport($period)
    {
        list($start, $end) = $period;
        $sql = "
            SELECT
                CLA.client_id,
                CLA.client_type_id,
                SAS.startdate,
               (
                CASE WHEN CLA.currency = 'UZS' THEN
                    sum2dollar(COALESCE(TP.tarif_price::real, SAS.tarif_price::real), '{$end}')
                ELSE
                    COALESCE(TP.tarif_price::real, SAS.tarif_price::real)
                END
               )::real AS tarif_price,
               SAS.reg_pay,
                (SELECT
                    modem_price
                FROM
                    modems
                WHERE
                    client_id = CLA.client_id
                    AND point_id = SAS.point_id limit 1) AS modem_price,
               (SELECT
                    T.tarif_name
                FROM
                    tarifs T
                WHERE
                    T.tarif_id = SAS.tarif_id) AS service_name
            FROM
                (SELECT
                    client_id, client_type_id, currency, client_dateagree
                 FROM
                    clients
                 UNION ALL
                 SELECT
                    client_id, client_type_id, currency, client_dateagree
                 FROM
                    clients_arhiv) CLA
                INNER JOIN (
                    SELECT
                        id, client_id, point_id, tarif_id, startdate, reg_pay, tarif_price, paidto
                    FROM
                    client_services_all
                    UNION ALL
                    SELECT
                        id, client_id, point_id, tarif_id, startdate, reg_pay, tarif_price, paidto
                    FROM
                        client_services_all_arhiv) SAS
                    ON CLA.client_id = SAS.client_id
                LEFT JOIN tarif_properties TP
                    ON TP.service_id = SAS.id
            WHERE
                CLA.client_dateagree BETWEEN '{$start}' AND '{$end}'
            ORDER BY
                CLA.client_dateagree
        ";
        return $this->_db->fetchAll($sql);
    }

 	public function getTotalCorpConnection($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				(CASE WHEN (
					SELECT
						COUNT(transactions.id)
					FROM
						transactions
					WHERE
						transactions.client_id = client_services.client_id
						AND transactions.trantype < 100
						AND transactions.summa > 0
					) > 0 THEN 1 ELSE 0 END)
					 AS is_payed,
 				COUNT(DISTINCT point_id) AS count_points,
 				SUM(
 					CASE WHEN discount > 0 THEN tarif_price * (1 -  discount / 100)
 					ELSE tarif_price END
 				) AS tarif,
 				SUM(reg_pay) AS reg_pay
 			FROM
 				(SELECT * FROM client_services UNION ALL SELECT * FROM client_services_arhiv) AS client_services
 			JOIN
 				(SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) AS clients
 				ON clients.client_id = client_services.client_id
 			WHERE
 				clients.client_dateagree BETWEEN '{$startDate}' AND '{$endDate}'
 				AND client_services.client_type_id = 0
 				AND client_services.statuscross IN (25, 1, 2, 3 ,4)
			GROUP BY
				is_payed
		";

		return $this->_db->fetchAll($sql);
 	}

 	public function getTotalPhysConnection($startDate, $endDate)
 	{
 		$sql = "
 			SELECT
 				COUNT(DISTINCT point_id) AS count_points,
 				SUM(
 					CASE WHEN discount > 0 THEN tarif_price * (1 -  discount / 100)
 					ELSE tarif_price END
 				) AS tarif,
 				SUM(reg_pay) AS reg_pay
 			FROM
 				(SELECT * FROM client_services UNION ALL SELECT * FROM client_services_arhiv) AS client_services
 			JOIN
 				(SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) AS clients
 				ON clients.client_id = client_services.client_id
 			WHERE
 				clients.client_dateagree BETWEEN '{$startDate}' AND '{$endDate}'
 				AND client_services.client_type_id = 1
 				AND client_services.statuscross IN (25, 1, 2, 3 ,4)
		";

 		$data = array();
 		$data[] = $this->_db->fetchRow($sql);

 		$sql = "
 			SELECT
 				COUNT(DISTINCT client_services.point_id) AS count_points,
 				SUM(transactions.summa) AS tarif
 			FROM
 				(SELECT * FROM client_services UNION ALL SELECT * FROM client_services_arhiv) AS client_services
 			JOIN
 				(SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) AS clients
 				ON clients.client_id = client_services.client_id
 			JOIN
 				transactions ON transactions.client_id = clients.client_id AND trantype = 1011
 			WHERE
 				clients.client_dateagree BETWEEN '{$startDate}' AND '{$endDate}'
 				AND client_services.client_type_id = 1
 				AND client_services.statuscross IN (25, 1, 2, 3 ,4)
		";

 		$data[] = $this->_db->fetchRow($sql);

 		return $data;
 	}

 	public function getPointLastAbonPrice($pointID)
 	{
 		if ( ! $pointID )
 		{
 			return 0;
 		}

 		$tables = array('adsl', 'wifi', 'vpn', 'tasix');

 		foreach ($tables as $table)
 		{
	 		$sql = "
	 			SELECT
	 				S.id,
	 				discount,
	 				COALESCE(TP.tarif_price::real, tarifs.tarif_price::real) AS tarif_price
	 			FROM
	 				{$table} S
	 			INNER JOIN tarifs
                    ON tarifs.tarif_id = S.tarif_id
                LEFT JOIN tarif_properties TP
                    ON TP.service_id = S.id
	 			WHERE
	 				S.point_id = {$pointID}
	 				AND S.startdate < current_date
	 			ORDER BY
	 				S.startdate DESC
	 			LIMIT 1
	 		";

	 		$res =  $this->_db->fetchRow($sql);

	 		if ( $res['id'] )
	 		{
		 		$amount = $res['tarif_price'];

		 		if ($res['discount'])
		 		{
		 			$amount = $amount * (100 - $res['discount']) / 100;
		 		}

		 		return $amount;
	 		}
 		}

 		return 0;
 	}

 	public function getPointsForUncross($is_stream)
 	{
 		$sql = "
 			SELECT
 				points.point_id,
 				points.u_login,
 				points.statuscross,
 				clients.client_name,
 				clients.client_dateagree,
 				clients.ballance_change,
 				clients.ballance
 			FROM
 				clients
 			JOIN
 				points ON points.client_id = clients.client_id
 			WHERE
 				points.statuscross < 0
 				AND points.statuscross > -25
 				AND client_type_id = {$is_stream}
 		";

 		return $this->_db->fetchAll($sql);
 	}

        public function getReportOffInclusions($start, $end, $is_stream)
        {
            $sql = "
    select
		distinct point_id,
		sent_date,
		receive_date,
		kind,

		sent_way,
		ats_id,
		date,
		pcross,
		ats_name,
		ballance,
		u_login,
		client_type_id,
		client_name,
		client_dateagree
    from
                    (select
                            LTA.*,
                            (select pcross from points where point_id = LTA.point_id) as pcross,
                            (select name from ats_list where id = LTA.ats_id) as ats_name,
                            (select clients.ballance from clients, points
                            where
                                        clients.client_id = points.point_id
                                    and points.point_id = LTA.point_id limit 1) as ballance,
                            (select u_login from points where point_id = LTA.point_id limit 1) as u_login,
                            (select clients.client_type_id from clients, points
                                    where
                                                clients.client_id = points.point_id
                                            and points.point_id = LTA.point_id limit 1) as client_type_id,
                            (select clients.client_name from clients, points
                                    where
                                                clients.client_id = points.point_id
                                            and points.point_id = LTA.point_id limit 1) as client_name,
                            (select clients.client_dateagree from clients, points
                                    where
                                                clients.client_id = points.point_id
                                            and points.point_id = LTA.point_id limit 1) as client_dateagree
                    from letters_to_ats as LTA
                    where
                            sent_date between '{$start}' and '{$end}'
                    ) as bind
                    where
                            bind.client_type_id = {$is_stream}
                    and
                            bind.kind in (2,3)

                            UNION

     select
	distinct point_id,
	sent_date,
	receive_date,
	kind,
	sent_way,
	ats_id,
	date,
	pcross,
	ats_name,
	ballance,
	u_login,
	client_type_id,
	client_name,
	client_dateagree
	from
                    (select
                            LTA.*,
                            (select pcross from points_arhiv where point_id = LTA.point_id) as pcross,
                            (select name from ats_list where id = LTA.ats_id) as ats_name,
                            (select clients_arhiv.ballance from clients_arhiv, points_arhiv
                                    where
                                                clients_arhiv.client_id = points_arhiv.point_id
                                            and points_arhiv.point_id = LTA.point_id limit 1) as ballance,
                            (select u_login from points_arhiv where point_id = LTA.point_id limit 1) as u_login,
                            (select clients_arhiv.client_type_id from clients_arhiv, points_arhiv
                                    where
                                                clients_arhiv.client_id = points_arhiv.point_id
                                            and points_arhiv.point_id = LTA.point_id limit 1) as client_type_id,
                            (select clients_arhiv.client_name from clients_arhiv, points_arhiv
                                    where
                                                clients_arhiv.client_id = points_arhiv.point_id
                                            and points_arhiv.point_id = LTA.point_id limit 1) as client_name,
                            (select clients_arhiv.client_dateagree from clients_arhiv, points_arhiv
                                    where
                                                clients_arhiv.client_id = points_arhiv.point_id
                                            and points_arhiv.point_id = LTA.point_id limit 1) as client_dateagree
                    from letters_to_ats as LTA
                    where
                            sent_date between '{$start}' and '{$end}'
                    ) as bind
                    where
                            bind.client_type_id = {$is_stream}
                    and
                            bind.kind in (2,3)
			";

             return $this->_db->fetchAll($sql);
        }

 	public function getCollacationPrice($sid)
 	{
		$sql = "
			select abon_price from collacation where id = {$sid}
		";
		return $this->_db->fetchOne($sql);
 	}

 	public function getUnresolvedCalls($startDate = null, $endDate = null)
 	{
 		$sql = "
 			SELECT DISTINCT
 				date_part('month', call_date) AS month,
				call_date,
				call_time,
				abonent1,
				abonent2,
				round((talk_sec::float + 29 ) / 60) ::float AS minutes_count
			FROM
				phone_services_calls
		  	WHERE
		  		talk_sec > 6
		  		AND abonent2::character varying LIKE '8%'
		  		AND abonent1::character varying LIKE '113%'
		  		AND abonent1 != 1131313
				AND abonent1 != 1130000
				AND abonent1::character varying NOT IN (
					SELECT \"number\" FROM isdn_numbers
					UNION ALL
					SELECT \"number\" FROM tradtel_numbers
					UNION ALL
					SELECT \"number\" FROM ngn_numbers
				)";

 		if ($startDate)
 		{
 			$sql .= " AND call_date >= '{$startDate}' ";
 		}

 		if ($endDate)
 		{
 			$sql .= " AND call_date <= '{$endDate}' ";
 		}

 		$sql .= " ORDER BY call_date, call_time";

 		$data =  $this->_db->fetchAll($sql);
 		$result = array();

 		foreach ($data as $item)
 		{
 			if ( ! is_array($result[$item['month']]) )
 			{
 				$result[$item['month']] = array();
 			}

 			array_push($result[$item['month']], $item);
 		}

 		return $result;
 	}

 	public function calculateQuantCalls($calls, $tarifs)
 	{
 		$quantCalls = array();

 		foreach ($calls as $call)
 		{
 			if ($call['abonent1'] == 1131313)
 			{
 				array_push($quantCalls, $call);
 			}
 		}

 		return $this->calulateCalls($quantCalls, $tarifs);
 	}

 	public function getCalls($calls, $tarifs)
 	{
 		$result = array();

 		foreach ($calls as $call)
 		{
 			$phone = $call['abonent2'];
 			$phoneLen = strlen($phone);

 			foreach ($tarifs as $tarif)
 			{
 				$prefix = $tarif['prefix'];
 				$prefixLen = strlen($prefix);

 				if ($phoneLen < $prefixLen + 3)
 				{
 					continue;
 				}

 				if (substr($phone, 0, $prefixLen) == $prefix
 					&& $call['call_date'] >= $tarif['start_date']
 					&& $call['call_date'] <= $tarif['end_date'])
 				{
 					if ( ! $tarif['price'] )
 					{
 						break;
 					}

					$call['directions'] = $tarif['directions'];
 					$call['amount'] += $call['minutes_count'] * $tarif['price'];
 					$call['price'] = $tarif['price'];
 					break;
 				}
 			}

 			array_push($result, $call);
 		}

 		return $result;
 	}


 	public function getSwitchOffClients($start, $end, $is_stream)
 	{
		$sql_streamOff = "
			select
				TH.*
			from tech_history  as TH, clients_arhiv as CA
			where
				TH.status in (-25)
			and
				date between '{$start}' and '{$end}'
			and
				TH.client_name not like '%TEST%'
			and
				TH.client_name = CA.client_name
			and
				CA.client_type_id = {$is_stream}
				order by TH.client_name
		";
		$arrResult['stream_off'] = $this->_db->fetchAll($sql_streamOff);
 	}

        public function getOverdraftClients()
        {
            $sql = "
		select
			CLA.client_id,
			CLA.client_name,
			CLA.ballance,
                        CLA.phone,
			'Клиенты c услугой' as title,
			CLA.overdraft,

			(select PHL.name from phone_hub_list  as PHL, ats_list as AL, points as PTS
			where
				PHL.id = AL.phone_hub_id
			and
				AL.id = PTS.ats_id
			and
				PTS.client_id = CLA.client_id order by PHL.id limit 1 ) as hub_name,
			(select PHL.id from phone_hub_list  as PHL, ats_list as AL, points as PTS
			where
				PHL.id = AL.phone_hub_id
			and
				AL.id = PTS.ats_id
			and
				PTS.client_id = CLA.client_id order by PHL.id limit 1 ) as hub_id

		from clients as CLA
		where
			CLA.client_type_id = 0
		and
			CLA.overdraft <> 0
		order by hub_id
            ";

            return $this->_db->fetchAll($sql);
        }

	public function getPrivateClients($group, $startDate, $endDate)
	{
		$sql = "
			SELECT
				clients.client_id,
				clients.client_name,
				tarifs.tarif_name,
				tarifs.speed,
				tarifs.traffic AS price,
				points.u_login,
				period_traffic(points.u_login, '{$startDate}', '{$endDate}') AS traffic
			FROM
				adsl
			JOIN
				points ON points.point_id = adsl.point_id
			JOIN
				clients ON clients.client_id = points.client_id
			JOIN
				tarifs ON tarifs.tarif_id = adsl.tarif_id
			WHERE
				adsl.startdate <= '{$endDate}'
				AND adsl.enddate >= '{$startDate}'
				AND tarifs.tarif_price = 0
				AND clients.client_type_id = 1
				AND adsl.is_deleted = false
		";

		if($group == "private-office")
		{
			$sql .= " AND tarifs.tarif_name like '%Private-office%' ";
		}
		elseif($group == "private")
		{
			$sql .= " AND tarifs.tarif_name like '%Private%'
					AND tarifs.tarif_name not like '%Private-office%' ";
		}

		$sql .= " ORDER BY clients.client_name ";

		return $this->_db->fetchAll($sql);
	}

	public function getWififClients($startDate, $endDate)
	{
		$sql = "
			SELECT
				clients.client_id,
				clients.client_name,
				tarifs.tarif_name,
				tarifs.speed,
				tarifs.traffic AS price,
				points.u_login,
				period_corp_traffic(points.point_id, '{$startDate}', '{$endDate}') AS traffic
			FROM
				wifi
			JOIN
				points ON points.point_id = wifi.point_id
			JOIN
				clients ON clients.client_id = points.client_id
			JOIN
				tarifs ON tarifs.tarif_id = wifi.tarif_id
			WHERE
				wifi.startdate <= '{$endDate}'
				AND wifi.enddate >= '{$startDate}'
				AND tarifs.tarif_price = 0
				AND wifi.is_deleted = false
		";

		return $this->_db->fetchAll($sql);
	}

	public function getReportDiscountClientsOldService($startDate, $endDate)
	{
		$tables = array('adsl', 'isdn', 'tasix');
		$result = array();

		foreach ($tables as $table)
		{
			$sql_tarifs = "
				SELECT
					clients.client_id,
					clients.client_name,
					tarifs.tarif_name,
					'{$table}' as service_name,
					tarifs.speed,
					tarifs.traffic AS price,
					points.u_login,
					period_corp_traffic(points.point_id, '{$startDate}', '{$endDate}') AS traffic
				FROM
					{$table}
				JOIN
					points ON points.point_id = {$table}.point_id
				JOIN
					clients ON clients.client_id = points.client_id
				JOIN
					tarifs ON tarifs.tarif_id = {$table}.tarif_id
				WHERE
					{$table}.startdate <= '{$endDate}'
					AND {$table}.enddate >= '{$startDate}'
					AND tarifs.tarif_price = 0
					AND clients.client_type_id = 0
					AND {$table}.is_deleted = false
					AND tarifs.tarif_name not like '%Private%'
				ORDER BY clients.client_name
			";

			$data = $this->_db->fetchAll($sql_tarifs);

			foreach ($data as $row)
			{
				array_push($result, $row);
			}
		}

		return $result;
	}

	public function getClientTarifDiscountOld()
	{
		$sql = "
			select * from tarifs
			where
				tarif_price = 0
			and
				servicetype_id in (7000, 8000, 7020, 8020, 7030)
		";
		$data = $this->_db->fetchAll($sql);
		return $data;
	}

	public function getDiscountClientsOLD()
	{
		$sql_tarifs = "
			select * from tarifs where is_disc  = true
		";
		$tarifs = $this->_db->fetchAll($sql_tarifs);



		$arrPuts = array();

		for($i = 0; $i < count($tarifs); $i++)
		{
			$sql_fetch_services = "
				select * from client_services where tarif_id = {$tarifs[$i]['tarif_id']}
			";
			$data = $this->_db->fetchAll($sql_fetch_services);

			foreach($data as $value)
			{
				array_push($arrPuts, $value);
			}
		}
		return $arrPuts;
	}


        public function getopticalClients($countryID = 0)
        {
            $sql = "
                select
                        count(CLA.client_id) as cc,
                        AL.expanded
                from points as PTS, clients as CLA, ats_list as AL
                where
                        CLA.client_id = PTS.client_id
                and
                        PTS.ats_id = AL.id
                and
                        CLA.client_type_id = 1
                and
                        PTS.country_id = {$countryID}
                group by AL.expanded
            ";
            return $this->_db->fetchAll($sql);
        }

        public function getShareStatistic($date)
        {
			$start = $date['start_year']. "-". $date['start_month']. "-". "01";

			$endDay = date('t', strtotime($start));

			$end = $date['start_year']. "-". $date['start_month']. "-". $endDay;

            $sql_trusPayments = "
                select count(id) from trust_payments
                where
                        to_char(date_action, 'YYYY-MM') = to_char('{$start}'::timestamp, 'YYYY-MM')
            ";
            $result['trust_clients'] = $this->_db->fetchOne($sql_trusPayments);


            $sql_sk = "
                select count(id) from card_action
                where
						to_char(activate_date, 'YYYY-MM') = to_char('{$start}'::timestamp, 'YYYY-MM')
            ";
            $result['sk'] = $this->_db->fetchOne($sql_sk);


			$start  = $start . " 00:00:00";
			$end    = $end . " 23:59:59";

			$megabutton_per_day = "
				select
					count(SAS.id) as count_act,
					(select tarif_name from tarifs where tarif_id = SAS.tarif_id and group_name = 'dop_service') as tarif_name,
					sum((select tarif_price from tarifs where tarif_id = SAS.tarif_id and group_name = 'dop_service')) as tarif_price
				from stream_additional_services as SAS
				where
					startdate >= '{$start}'
				and
					enddate <= '{$end}'
				group by tarif_name
			";

			$result['megabutton_per_day'] = $this->_db->fetchAll($megabutton_per_day);

			$megabutton_per_month = "
				select
					count(SAS.id) as count_act,
					(select tarif_name from tarifs where tarif_id = SAS.tarif_id and group_name = 'dop_service_per_month') as tarif_name,
					sum((select tarif_price from tarifs where tarif_id = SAS.tarif_id and group_name = 'dop_service_per_month')) as tarif_price
				from stream_additional_services as SAS
				where
					startdate >= '{$start}'
				and
					enddate <= ('{$end}'::timestamp + INTERVAL '1 month')
				group by tarif_name
			";

			$result['megabutton_per_month'] = $this->_db->fetchAll($megabutton_per_month);

            return $result;
        }

        public function getRealisationClients($date)
        {
            $start = $date['start_year']."-".$date['start_month']."-".$date['start_day'];
            $end   = $date['end_year']."-".$date['end_month']."-".$date['end_day'];

			$sql = "
				select
					(select client_name from clients where client_id = CO.client_id) as client_name,
					COD.*,
					CO.*
				from
					card_orders_details as COD,
					card_orders as CO
				where
					CO.id = COD.order_id
					and CO.order_date between '{$start}' and '{$end}'
					and CO.is_posted = true
					and CO.is_deleted = false
			";

			return $this->_db->fetchAll($sql);
        }

		public function getDynamicRegistry($date, $is_stream)
		{
			if($is_stream == "0")
			{
				$arr = array();

				$sql = "
					SELECT
						to_char(last_date, 'MM') AS month,
						servicetype_name,
						registry.service_type,
						amount,
						clients_count,
						points_count
					FROM
						registry
					LEFT JOIN
						service_type ON registry.service_type = service_type.servicetype_id
					WHERE
						to_char('{$date}'::timestamp, 'YYYY') = to_char(last_date::timestamp, 'YYYY')
				";

				$data = $this->_db->fetchAll($sql);

				foreach ($data as $value)
				{
					$arr[$value['service_type']][$value['month']] = $value;
				}

				return $arr;
			}

			if($is_stream == "1")
			{
				$sql = "
						select
							sum(T.summa) as amount,
							count(T.client_id) as count_client_id,
							to_char(T.currenttime, 'MM') as mon
						from transactions as T, clients as CLA
						where
							T.client_id = CLA.client_id
						and
							CLA.client_type_id = 1
						and
							T.trantype = 1001
						and
							to_char('{$date}'::timestamp, 'YYYY')  = to_char(T.currenttime, 'YYYY')
						group by mon
				";

				$data = $this->_db->fetchAll($sql);

				foreach($data as $key => $value)
				{
					$arr[$value['mon']] = $value;
				}

				return $arr;
			}
		}

	public function getClientsCount($clientType, $date)
	{
		$serviceTypeModel = new ServiceType();
		$tables = $serviceTypeModel->getServicesTables();

		$clients = array();

		foreach ($tables as $tableName)
		{
			if ( ! $tableName
    			|| $tableName == 'ivr'
    			|| $tableName == 'tel'
    			|| $tableName == 'lvs') // ivr, tel, lvs does not exist now
    		{
    			continue;
    		}

			$sql = "
				SELECT
    				clients.client_id
            	FROM
                	{$tableName}
                JOIN
                	(select * from points
                	union all select * from points_arhiv) AS points
                	ON points.point_id = {$tableName}.point_id
                JOIN
 					(select * from clients
 					union all select * from clients_arhiv ) AS clients
 					ON clients.client_id = points.client_id
            	JOIN
               		tarifs ON tarifs.tarif_id = {$tableName}.tarif_id
            	WHERE
                	clients.client_type_id = {$clientType}
                	AND clients.is_donate = false
                	AND clients.is_employee = false
                	AND {$tableName}.startdate <= '{$date}'
              		AND {$tableName}.enddate > '{$date}'
              		AND {$tableName}.is_deleted = false
			";

			if ($clientType === 0)
	        {
				$sql .= "AND {$tableName}.is_forced = true";
	        }

	        $data = $this->_db->fetchAll($sql);

	        foreach ($data as $item)
	        {
	        	$clients[$item['client_id']] = $item['client_id'];
	        }
		}

        return count($clients);
	}


	public function financeReportStats($start, $end, $invDate)
	{
		$start = $start." 00:00:00";
		$end   = $end." 23:59:59";
		$total = array();

		$sql_reestr = "
			select
				servicename,
				sum(
                    case when CLA.currency = 'UZS' then
                        sum2dollar(IDL.total::real, '{$invDate}')
                    else
                        IDL.total
                    end
				)::real as summa
			from
                invoices as I
                join invoice_details as IDL
                    on I.invoice_id = IDL.invoice_id
                join (select * from clients
                    union all select * from clients_arhiv) as CLA
                    on I.client_id = CLA.client_id
			where
				I.lastdate = '{$invDate}'
                and IDL.total != 0
                and not CLA.vip
                and not CLA.is_employee
                and not CLA.is_donate
			group by servicename
		";

		$resultReestr = $this->_db->fetchAll($sql_reestr);
        $totalRecalc = 0.0;
        foreach ($resultReestr as $row)
        {
            if($row['servicename'] == "Зачисление после пересчета" ||
               $row['servicename'] == "Снятие при пересчете" ||
               $row['servicename'] == "Снятие при пересчете 24" ||
               $row['servicename'] == "Смена тарифного плана")
            {
                $totalRecalc += $row['summa'];
            }
        }
        array_push($resultReestr, array('servicename' => 'Пересчеты',
                                       'summa' => $totalRecalc));
		$total[0] = $resultReestr;

		$sql_sharq = "
			select
				(select
                    typename
                from
                    asrtypes
                where
                    typename_value = '13'
                    and typename_id = T.trantype limit 1) as typename_value,
                sum(T.summa),
                T.trantype
			from
                (select * from clients
                union all
                select * from clients_arhiv) as CLA
                inner join  transactions as T
                    on CLA.client_id = T.client_id
            where
				CLA.client_type_id = 1
			and
				T.currenttime between '{$start}' and '{$end}'
			and
				T.trantype in (3, 6, 9, 20, 40, 44, 77)
			group by T.trantype
		";
		$resultSharq = $this->_db->fetchAll($sql_sharq);
		$total[1] = $resultSharq;


		$sql_cards = "
			select
				sum(sum2dollar(COD.amount::real,CO.order_date))
			from card_orders as CO, card_orders_details as COD
			where
				CO.id = COD.order_id
			and
				CO.order_date between '{$start}' and '{$end}'
			and
				CO.is_posted = true
		";
		$resultCard = $this->_db->fetchAll($sql_cards);
		$total[2] = $resultCard;

		return $total;
	}

	public function getCrossedLegalClients($clientTypeID)
	{
		$sql = "
			select PTS.client_id, PTS.u_login,  PIA.ip_address, DL.ip_address as ip_address_dslam, PRT.number,
				(select typename from asrtypes where typename_value = '5' and typename_id = DL.type_id) as dslam_type
			from ports as PRT, points as PTS, clients as CLA, dslam_list as DL, point_ip_addresses as PIA
			where
				PRT.id = PTS.port_id
			and
				PTS.client_id = CLA.client_id
			and
				CLA.client_type_id = {$clientTypeID}
			and
				PTS.point_id = PIA.point_id
			and
				now() between PIA.start_date and PIA.end_date
			and
				PRT.dslam_id = DL.id
			order by PTS.client_id
		";
		return $this->_db->fetchAll($sql);
	}

    public function getTarifInfoByRegions($startDate, $endDate, $countryId)
    {
        $sql = "
            SELECT
                tp.client_type_id,
                t.tarif_name,
                tp.tarif_id,
                tp.price,
                tp.amount,
                tov.overlimit
            FROM
                (SELECT
                    s.client_type_id,
                    s.tarif_id,
                    count(*) AS amount,
                    sum(get_real_abon_price(s.tarif_price, tp.tarif_price, s.currency, '{$endDate}')) AS price
                FROM
                    (SELECT * FROM client_services_all
                     UNION ALL
                     SELECT * FROM client_services_all_arhiv) s
                    LEFT JOIN adsl a
                        ON s.point_id = a.point_id
                        AND s.startdate = A.startdate
                        AND s.enddate = A.enddate
                    LEFT JOIN tarif_properties tp
                        ON tp.service_id = a.id
                WHERE
                    s.startdate <= '{$startDate}'
                    AND s.enddate > '{$startDate}'
                    AND ((now() BETWEEN s.startdate AND s.enddate AND NOT s.is_deleted)
                         OR (now() NOT BETWEEN s.startdate AND s.enddate AND s.is_deleted))
                    AND s.client_type_id in (0, 1)
                    AND s.country_id = {$countryId}
                GROUP BY s.client_type_id, s.tarif_id) tp
                LEFT JOIN
                (SELECT
                    s.tarif_id,
                    sum(summa) AS overlimit
                 FROM
                     (SELECT * FROM client_services
                      UNION ALL
                      SELECT * FROM client_services_arhiv) s
                     INNER JOIN transactions t
                        ON t.client_id = s.client_id
                 WHERE
                     s.startdate <= '{$startDate}'
                     AND s.enddate > '{$startDate}'
                     AND ((now() BETWEEN s.startdate AND s.enddate AND NOT s.is_deleted)
                          OR (now() NOT BETWEEN s.startdate AND s.enddate AND s.is_deleted))
                     AND s.client_type_id in (0, 1)
                     AND s.country_id = {$countryId}
                     AND t.currenttime::date BETWEEN '2010-12-01' AND '2010-12-31'
                     AND t.trantype = 1003
                 GROUP BY s.tarif_id) tov
                ON tp.tarif_id = tov.tarif_id
                INNER JOIN tarifs t
                     ON tp.tarif_id = t.tarif_id
                ORDER BY t.tarif_name
        ";
                     
        return $this->_db->fetchAll($sql);
    }

    public function getClientsByRegionAndTarif($countryId, $tarifId, $startDate, $endDate)
    {
        $sql = "
            SELECT
                s.client_id, s.client_name, s.ballance
            FROM
                (SELECT * FROM client_services_all
                     UNION ALL
                     SELECT * FROM client_services_all_arhiv) s
            WHERE
                s.startdate <= '{$startDate}'
                AND s.enddate > '{$startDate}'
                AND ((now() BETWEEN s.startdate AND s.enddate AND NOT s.is_deleted)
                      OR (now() NOT BETWEEN s.startdate AND s.enddate AND s.is_deleted))
                AND s.tarif_id = {$tarifId}
                AND s.country_id = {$countryId}
        ";

        return $this->_db->fetchAll($sql);
    }
}
