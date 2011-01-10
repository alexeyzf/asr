<?php
require_once('Zend/Db/Table.php');
require_once('ClientModel.php');

class FinanceModel extends Zend_Db_Table
{
    protected $_name = 'transactions';
    protected $_sequence = 'transactions_id_seq';

    public function getTransactionList($client_id, $data = "")
    {

        $sqlList = "
            select
                T.*,
                (select typename from transaction_type
                where typename_id = T.trantype LIMIT 1) as tran_label,
                (select client_name from clients where client_id = T.client_id) as client_name,
                (select first_name ||' '|| last_name from users
                where id = T.userid) as user_name,
                (select service_type.servicetype_name from service_type
                where service_type.servicetype_id = T.servicetype) AS servicetype_name
            from transactions as T
            where
                T.client_id = {$client_id}
        ";
        if(!$data)
        {

            $sqlList .= " order by currenttime DESC ";

        }
        else
        {
            $start = $data['year'].$data['month'].$data['day'];
            $end   = $data['yearend'].$data['monthend'].$data['dayend'];

            $sqlList .= " and T.currenttime between '{$start}' AND '{$end}'";
        }

        return $this->_db->fetchAll($sqlList);
    }

    public function getSortedTransactions($clientID, $year, $month)
    {
    	$endDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));

    	return $this->fetchAll("client_id = {$clientID}
    							AND currenttime > '{$year}-01-01'
    							AND currenttime <= '{$endDate}'",
    							'currenttime')->toArray();
    }

    public function addTransaction($clientID, $serviceType, $transactionType, $amount, $userID = NULL, $pointID = null, $commente = NULL)
    {
        $data['client_id']   = $clientID;
        $data['servicetype'] = $serviceType;
        $data['trantype'] 	 = $transactionType;
        $data['summa']    	 = $amount ? $amount : '0';
        $data['summas']   	 = new Zend_Db_Expr("dollar_to_sum({$data['summa']})");
        $data['userid']   	 = $userID;
        $data['point_id']    = $pointID;
        $data['commente']    = $commente;
        $this->insert($data);

        $clientData['ballance'] = new Zend_Db_Expr('ballance ' . ($transactionType < 100 ? '+' : '-') . $data['summa']);


        $clientModel = new ClientModel();
        $clientModel->update($clientData, "client_id = {$clientID}");
    }

    public function getSaldo($client_id)
    {
        $sql = "
        select
            BL.*,
            (select client_name from clients where client_id = BL.clientid) as client_name
        from ballance_logs as BL where clientid = {$client_id}
        order by BL.cur_date desc
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getTraffic($client_id, $data)
    {
        $sql = "
            SELECT
                SUM(acctinputoctets) / 1024 / 1024 as traffic_in,
                SUM(acctoutputoctets) / 1024 / 1024 as traffic_out

            FROM
                radacct
            WHERE
                 radacct.username = (select u_login from points where client_id = {$client_id})
            ";

        if(!$data)
        {
            $sql .= " ";
        }
        else
        {
            $start = $data['year'].$data['month'].$data['day'];
            $end   = $data['yearend'].$data['monthend'].$data['dayend'];

            $sql .= " and acctstarttime between '{$start}' AND '{$end}'";

        }
        //var_dump($sql);
        //exit();
        return $this->_db->fetchRow($sql);
    }

	public function checkTransactionOnExist($clientID, $date, $commente)
	{
		$sql = "
			SELECT
				id
			FROM
				transactions
			WHERE
				commente = '{$commente}'
				AND client_id = {$clientID}
				AND currenttime::character varying like '{$date}%'
				AND (trantype=20 or trantype=21)";

		$ID = $this->_db->fetchOne($sql);

		if ($ID)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function addRow($clientID, $tranType, $amountUsd, $amountUzs, $serviceType, $time, $notes, $currency = "")
	{
		$data['client_id'] = $clientID;
		$data['trantype'] = $tranType;

		if($currency == "UZS")
		{
			$data['summa'] = $amountUzs;
		}
		else
		{
			$data['summa'] = $amountUsd;
		}

		$data['summas'] = $amountUzs;
		$data['servicetype'] = $serviceType;
		$data['userid'] = 0;
		$data['currenttime'] = $time;
		$data['commente'] = $notes;

		$this->insert($data);
	}


	public function getBallancesOnDate($date)
	{
		$sql = "
			SELECT
				client_id,
				SUM((
					CASE
						WHEN trantype < 100 THEN summa
						ELSE -summa
					END
					)) AS ballance
			FROM
				transactions
			WHERE
				currenttime <= '{$date}'
			GROUP BY
				client_id
		";

		return $this->_db->fetchPairs($sql);
	}

	public function getAbonPrice($clientID, $startDate, $endDate, $pointID = null)
	{
		$sql = "
			SELECT
				SUM(summa)
			FROM
				transactions
			WHERE
					client_id = {$clientID}
				AND
					currenttime BETWEEN '{$startDate}' AND '{$endDate}'
				AND
					(trantype = 1001 OR trantype = 1005)
		";

		if($pointID)
		{
			$sql .= "
				AND point_id = {$pointID}
			";
		}

		return $this->_db->fetchOne($sql);
	}

	public function getTransactions($clientID = null, $trantype = null, $serviceType = null, $startDate = null, $endDate = null, $pointID = null)
	{
		$select = $this->_db->select()->from('transactions');

		if ($clientID)
		{
			$select = $select->where("client_id = {$clientID}");
		}

		if ($trantype)
		{
			if ( is_array($trantype) )
			{
				$select = $select->where('trantype IN (' . implode(',', $trantype) . ')');
			}
			else
			{
				$select = $select->where("trantype = {$trantype}");
			}
		}

		if ($serviceType)
		{
			$select = $select->where("servicetype = {$serviceType}");
		}

		if ($startDate)
		{
			$select = $select->where("currenttime >= '{$startDate}'");
		}

		if ($endDate)
		{
			$select = $select->where("currenttime < '{$endDate}'");
		}

		if ($pointID)
		{
			$select = $select->where("point_id = {$pointID}");
		}

		return $select->query()->fetchAll();
	}

	public function getBankClient($date)
	{
		$sql = "
		SELECT
			TR.*,
			(SELECT notes FROM bank_client WHERE client_id = clients.client_id AND doc_num = TR.commente LIMIT 1) AS nazplat,
			(SELECT account FROM bank_client WHERE client_id = clients.client_id AND doc_num = TR.commente LIMIT 1) AS account,
			(SELECT contract_number FROM contracts WHERE client_id = clients.client_id AND contract_type_id = 1) AS dogovor,
			(SELECT dateagree FROM contracts WHERE client_id = clients.client_id AND contract_type_id = 1) AS dogovor_date,
			clients.client_type_id
		FROM
			transactions as TR
		JOIN
			clients ON clients.client_id = TR.client_id
		WHERE
			(TR.trantype = 20
			OR TR.trantype = 21)
			AND TR.currenttime = '{$date}'
		";

		return $this->_db->fetchAll($sql);
	}

	public function getCardClientIncomeAmount($startDate, $endDate)
	{
		$startDate = $startDate. " 00:00:00";
		$endDate   = $endDate. " 23:59:59";

		$sql = "
			SELECT
				sum(summa) AS amount_usd,
				sum(summas) AS amount_uzs
			FROM
				{$this->_name}
			JOIN
				clients ON clients.client_id = transactions.client_id
			WHERE
				clients.client_type_id = 4
				AND currenttime between '{$startDate}' AND '{$endDate}'
		";

		return $this->_db->fetchRow($sql);
	}

	public function getCardClientIncomeAmountByMonth($startDate, $endDate)
	{
		$startDate = $startDate. " 00:00:00";
		$endDate   = $endDate. " 23:59:59";

		$sql = "
			SELECT
				date_trunc('month', currenttime) AS month,
				sum(summa) AS amount_usd,
				sum(summas) AS amount_uzs
			FROM
				{$this->_name}
			JOIN
				clients ON clients.client_id = transactions.client_id
			WHERE
				clients.client_type_id = 4
				AND currenttime between '{$startDate}' AND '{$endDate}'
			GROUP BY
				date_trunc('month', currenttime)
		";

		return $this->_db->fetchAll($sql);
	}

	public function getPaymentFromCashDesks($code, $country)
	{
		/**
		 *  return trantype
		 */
		 $sql = "
			select typename_id from asrtypes where typename_value = '13'
			and
				a_value = {$code}
			and
				b_value = {$country}
		 ";
		 return $this->_db->fetchOne($sql);
	}

	public function getCurrencyCode($clientID)
	{
		$sql = "
			select currency from clients where client_id = {$clientID}
		";
		$currency = $this->_db->fetchOne($sql);
		if($currency == "UZS")
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}


	public function startTransaction()
	{
		$this->_db->beginTransaction();
	}

	public function commitTransaction()
	{
		$this->_db->commit();
	}

	public function rollBackTransaction()
	{
		$this->_db->rollBack();
	}

	public function decreaseAmount($id, $summ)
	{
		$data['summa'] = new Zend_Db_Expr("summa - {$summ}");
		$data['summas'] = new Zend_Db_Expr("dollar2sum(summa - {$summ})");

		$this->update($data, "id = {$id}");
	}
}