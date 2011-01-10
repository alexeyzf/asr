<?php
require_once 'Zend/Db/Table/Abstract.php';

class SaldoModel extends Zend_Db_Table_Abstract
{

	protected $_name = 'ballance_logs';

	public function getSaldoFromDate($date, $client_id)
	{
		$sql = "
			select
				BL.*,
				(select client_name from clients where client_id = BL.clientid) as client_name
			from ballance_logs as BL
			where
				BL.clientid  = {$client_id}
			and
				BL.cur_date = '{$date}'
		";

		return $this->_db->fetchAll($sql);
	}

	public function setSaldoNew($newsaldo, $id)
	{
		$sql = "
			update ballance_logs set ballance = {$newsaldo}
			where
				id = {$id}
		";
		$this->_db->fetchAll($sql);
	}

	public function setRealBallance($clientID, $correctSaldo)
	{
		$sql = "
			select
				sum(
				(CASE
					WHEN trantype > 100 THEN
						-T.summa
					ELSE
						T.summa
				END)) as summa_main
			from transactions as T
			where
				T.client_id = {$clientID}
			and
				T.currenttime between '2010-01-01 00:00:00' and '2011-01-01 23:59:59'
		";

		$transactionsAmount = $this->_db->fetchOne($sql);

		$result = $correctSaldo + $transactionsAmount;

		$this->updateBallance($clientID, $result);

		$sql_correct = "
			insert into correction (cid, is_added) values ({$clientID}, false)
		";
		$this->_db->fetchAll($sql_correct);
	}


	public function updateBallance($clientID, $trueBallance)
	{
		$sql = "
			update clients set ballance = {$trueBallance} where client_id = {$clientID}
		";
		$this->_db->fetchAll($sql);
	}
}
