<?php
require_once('Zend/Db/Table.php');

class InvoiceModel extends Zend_Db_Table
{
    protected $_name = 'invoices';
    protected $_sequence = 'invoices_invoice_id_seq';

	public function getAllCorpClientsInvoices()
	{
		$sql = "
            SELECT
                client_id
            FROM
                clients
            WHERE client_type_id = 0
            ORDER BY client_id
		";
		return $this->_db->fetchAll($sql);
	}

	public function getServiceByID($client_id, $tablename)
	{
			$query = "
			select
			    CLA.*,
				PTS.point_id,
				PTS.u_login,
			    COLLA.id,
			    COLLA.tarif_id,
			    COLLA.point_id,

			    (select tarif_name from tarifs where
				tarif_id = COLLA.tarif_id
			    ) as tarif_name,

			    (select tarif_price from tarifs where
				tarif_id = COLLA.tarif_id
			    ) as tarif_price,

			    (select traffic from tarifs where
				tarif_id = COLLA.tarif_id
			    ) as traffic,

				(select contract_number from contracts
				where client_id = CLA.client_id and contract_type_id = 1) as contract_number,

			    (select servicetype_name from service_type, tarifs
			    where
				service_type.servicetype_id = tarifs.servicetype_id
				and
				tarifs.tarif_id = COLLA.tarif_id
			    ) as servicetype_name,

			    (select unit_type from service_type, tarifs
			    where
				service_type.servicetype_id = tarifs.servicetype_id
				and
				tarifs.tarif_id = COLLA.tarif_id
			    ) as unit_type,

			    (select short_name from service_type, tarifs
			    where
				service_type.servicetype_id = tarifs.servicetype_id
				and
				tarifs.tarif_id = COLLA.tarif_id
			    ) as short_name,

			    (select ss.servicetype_id from service_type ss, tarifs as tt
			    where
				ss.servicetype_id = tt.servicetype_id
				and
				tt.tarif_id = COLLA.tarif_id
			    ) as servicetype_id,

				(select typename from asrtypes
			    where typename_value = '3' and typename_id = CLA.bank_id
			    ) as bank_name,

			    (select typename from asrtypes
			    where typename_value = '1' and typename_id = CLA.town
			    ) as country,

				(select contract_number from contracts where client_id = CLA.client_id and contracts.contract_type_id = 1)
				as contract_number,

   				(select dateagree from contracts where client_id = CLA.client_id and contracts.contract_type_id = 1)
				as dateagree_c

			from
			    $tablename as COLLA
			    left join
				points as PTS on(COLLA.point_id = PTS.point_id)
			    left join
				clients as CLA on(PTS.client_id = CLA.client_id)
			where
					current_date between COLLA.startdate and COLLA.enddate
				and
					COLLA.enddate > current_date
				and
					COLLA.is_deleted = true
				and
					CLA.client_type_id = 0
				and
					CLA.client_id = $client_id
				and
					COLLA.tarif_id is not null
		";
		return $this->_db->fetchAll($query);
	}

	public function getTraffic($login, $startDate, $endDate)
    {
        $sql = "
            SELECT
                nas.servicetype AS servicetype,
                SUM(acctinputoctets)  / 1024 / 1024 AS output,
                SUM(acctoutputoctets) /1024 / 1024 AS input
            FROM
                radacct
            LEFT JOIN
                nas on nas.ipaddr = radacct.nasipaddress
            WHERE
                username = '{$login}'
                AND acctstoptime > '{$startDate}'
                AND acctstoptime < '{$endDate}'
            GROUP BY nas.servicetype
        ";

		//$result = pg_query($conn, $sql) or die("Error in query: $query." . pg_last_error($conn));
		$result = $this->_db->fetchAll($sql);

		for( $i = 0; $i < count($result); $i++ )
		{
			$test[$result[$i]['servicetype']] = array(
				'input'	 => $result[$i]['input'],
				'output' => $result[$i]['output']
			);
		}

		return $test;
    }


    public function insertInvoice( $client_id, $contract_number, $lastdate)
	{
		$nextSF = $this->getNextSF();

		$sqlSchet = "select (count(client_id) + 1) as schet from invoices where client_id = {$client_id}; ";


		// ТУТ ERROR
		$schet_count = $this->_db->fetchRow($sqlSchet);


		$num = $schet_count['schet'];

		$schet_number = $contract_number. '-'. $num;


		$sql = "
			insert into invoices (client_id, schetfnum, schetnum, lastdate)
				values
					(
						 {$client_id},
						'{$nextSF}',
						'{$schet_number}',
						'{$lastdate}'
					)
		";


		$this->_db->fetchAll($sql);

		$sql_last_id = "SELECT currval('invoices_invoice_id_seq') as lastid";

		$last_id = $this->_db->fetchRow($sql_last_id);

		return $last_id['lastid'];

	}


	public function getNextSF()
	{
		/**
		 *  Метод ретурнит след. номер счёт фактуры
		 */

		$sqlSFnumber = "
			SELECT
				max(to_number(schetfnum, '99999999')) AS maxnumber
			FROM
				invoices
			WHERE
				schetfnum <> ''
			and
				schetfnum is not null
		";

		$result = $this->_db->fetchRow($sqlSFnumber);
		if($result['maxnumber'] == "")
		{
			return 1;
		}
		else
		{
			return $result['maxnumber'] + 1;
		}
	}

	public function getTariffExcess($tarif_id)
	{
		$sql = "
			select traffic_excess from tarif_components
			where
				tarif_id = {$tarif_id}
			and
				traffic_excess is not null
		";

		$tarif_excess = $this->_db->fetchRow($sql);
		return $tarif_excess['traffic_excess'];
	}


	public function insertInvoiceDetails($data)
	{
		$total = $data['excess'] + $data['tarif_price'];

		if(!$data['excess'])
		{
			$data['excess'] = 0;
		}

		$sqlInsertDetails = "
		insert into invoice_details (name, unit, quantity, price, amount, amount_nds, amount_aksiz, overlimit, total, invoice_id, servicename)
			values
				(
					'{$data['month']}',
					'{$data['unit_type']}',
					1,
					{$data['tarif_price']},
					{$data['tarif_price']},
					0,
					0,
					{$data['excess']},
					{$total},
					{$data['last_invoice_id']},
					'{$data['servicetype_name']}'
				)
		";

		$this->_db->fetchAll($sqlInsertDetails);
	}

	public function getInvoice($month, $year, $client_id = NULL, $exclude_avoiders = false)
	{
		$startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
		$endDate = date('Y-m-01', strtotime("+1 month {$year}-{$month}-01"));

		/**
		 *  метод вернет счет-фактуру за определенный месяц для указанного клиента
		 */

		$sql = "
			SELECT
				INV.*,
				CLA.*,
				(
					SELECT
						contract_number
					FROM
						contracts
					WHERE
						client_id = CLA.client_id and contract_type_id = 1
				) AS contract_number,
				(
					SELECT
						dateagree
					FROM
						contracts
					WHERE
						client_id = CLA.client_id and contract_type_id = 1
				) AS contract_date,
				(
					SELECT
						country_id
					FROM
						points
					WHERE
						client_id = CLA.client_id limit 1
				) AS country_id
			FROM
				invoices INV
                INNER JOIN clients CLA
                    ON INV.client_id = CLA.client_id
                LEFT OUTER JOIN invoices_avoiders INVA
                    ON INV.client_id = INVA.client_id
            WHERE
                INV.client_id = CLA.client_id
                AND INV.lastdate between '{$startDate}' AND '{$endDate}'
                AND CLA.vip = false
                AND CLA.is_employee = false
                AND CLA.is_donate = false
                AND CLA.inn IS NOT NULL
                AND CLA.inn != ''
		";

        if ($exclude_avoiders)
        {
            $sql .= "
                AND (INVA.id IS NULL OR INVA.startdate >= INV.currenttime::date)
            ";
        }

		if($client_id)
		{
			$sql .= "
                AND INV.client_id = {$client_id}  ORDER BY CLA.client_id
			";
		}
		else
		{
			$sql .= "
                ORDER BY CLA.client_id
			";
		}

		return $this->_db->fetchAll($sql);
	}

	public function getServiceForSchet($invoice_id)
	{
		/**
		 *  Метод ретурнит все услуги для указанной счёт-фактуры
		 */
		 $sql = "
		    select
				invoice_id,
				sum(total) as total,
				sum(quantity) as quantity,
				name,
				unit,
				servicename,
				price,
				amount,
				sum(total_uzs) as total_uzs,
				sum(overlimit) as overlimit,
				traffic_overlimit_price,
				sum(traffic_overlimit) as traffic_overlimit
			from invoice_details as IDT
			where
				IDT.invoice_id = {$invoice_id}
			group by  name, unit, servicename, price, amount, invoice_id, traffic_overlimit_price
		 ";
		 return $this->_db->fetchAll($sql);
	}

	public function getMoney($summas)
	{
		$sql = "
		select dollar2sum({$summas});
		";
		return $this->_db->fetchOne($sql);
	}

	public function getSchetData($clientID, $lastDate)
	{
		$sql = "
			SELECT
				schets.*,
				service_type.servicetype_name
			FROM
				schets
			JOIN
				service_type ON service_type.servicetype_id = schets.servicetype_id
			WHERE
				client_id = {$clientID}
				AND lastdate = '{$lastDate}'
		";

		$data = $this->_db->fetchAll($sql);

		return  $data;
	}

	public function getOverLimit($invoice_id)
	{
		$sql = "
		select * from invoice_details
		where
			invoice_id = {$invoice_id}
		and
			traffic_overlimit > 0
		and
			traffic_overlimit_price > 0
		";
		return $this->_db->fetchAll($sql);
	}

	public function getPointAmounts($pointID, $year, $month)
	{
		$sql = "
			SELECT
				date_part('month', invoices.lastdate) AS lmonth,
				schetfnum AS number,
				sum(invoice_details.total) AS amount_usd,
				sum(invoice_details.total_uzs) AS amount_uzs
			FROM
				invoices
			JOIN
				invoice_details ON invoice_details.invoice_id = invoices.invoice_id
			WHERE
				invoice_details.point_id = {$pointID}
				AND date_part('year', invoices.lastdate) = {$year}
				AND date_part('month', invoices.lastdate) <= {$month}
				AND invoice_details.servicename != 'Доставка счета и счёт-фактур'
			GROUP BY
				date_part('month', invoices.lastdate), schetfnum
		";

		$invoices = $this->_db->fetchAll($sql);

		$result = array();

		foreach ($invoices as $invoice)
		{
			$result[ intval($invoice['lmonth']) ] = $invoice;
		}

		return $result;
	}

	public function getClientAmounts($clientID, $year, $month)
	{
		$sql = "
			SELECT
					date_part('month', invoices.lastdate) AS lmonth,
					schetfnum AS number,
				(
					SELECT sum(invoice_details.total)
					FROM invoice_details
					WHERE invoices.invoice_id = invoice_details.invoice_id
				) AS amount_usd,
				(
					SELECT sum(invoice_details.total_uzs)
					FROM invoice_details
					WHERE invoices.invoice_id = invoice_details.invoice_id
				) AS amount_uzs
			FROM
				invoices
			WHERE
				invoices.client_id = {$clientID}
				AND date_part('year', invoices.lastdate) = {$year}
				AND date_part('month', invoices.lastdate) <= {$month}
			ORDER BY
				invoices.lastdate
		";

		$invoices = $this->_db->fetchAll($sql);

		$result = array();

		foreach ($invoices as $invoice)
		{
			$result[ intval($invoice['lmonth']) ] = $invoice;
		}
		return $result;
	}

	public function getInvoicesTotalAmounts($lastDate)
 	{
 		$sql = "
 			SELECT
 				clients.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				clients.address,
 				clients.phone,
 				sum(invoice_details.total) AS amount,
 				sum(invoice_details.total_uzs) AS amount_uzs
 			FROM
 				invoices
 			JOIN
 				(select * from clients
 				union all select * from clients_arhiv) AS clients
 				ON  clients.client_id = invoices.client_id
 			JOIN
 				invoice_details ON invoice_details.invoice_id = invoices.invoice_id
 			WHERE
 				invoices.lastdate = '{$lastDate}'
 			GROUP BY
 				clients.client_id,
 				invoices.schetfnum,
 				clients.client_name,
 				clients.address,
 				clients.phone
 			ORDER BY clients.client_id
 		";

 		$invoices = $this->_db->fetchAll($sql);
		$result = array();

 		foreach ($invoices as $invoice)
 		{
 			$result[$invoice['client_id']] = $invoice;
 		}

 		return $result;
 	}


 	public function getInvoiceDetailsCount($invoiceID)
 	{
 		$sql = "
 				select count(id) as ic from invoice_details  where invoice_id = {$invoiceID}
 		";
 		return $this->_db->fetchOne($sql);
 	}


 	public function getInvoicesFor2Month($first, $last, $clientID)
 	{
		$sql = "
			(select
				*
			from invoices as INV, invoice_details as INVD, clients as CLA
			where
				INV.lastdate = '{$last}'
			and
				INV.invoice_id = INVD.invoice_id
			and
				INV.client_id = CLA.client_id
			and
				CLA.client_id = {$clientID}
			order by INVD.servicename)
			UNION ALL
			(select
				*
			from invoices as INV, invoice_details as INVD, clients as CLA
			where
				INV.lastdate = '{$first}'
			and
				INV.invoice_id = INVD.invoice_id
			and
				INV.client_id = CLA.client_id
			and
				CLA.client_id = {$clientID}
			order by INVD.servicename)
		";

		$record = $this->_db->fetchAll($sql);
		return $record;

 	}

}