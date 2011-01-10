<?php
require_once('BaseModel.php');
require_once('ClientHelper.php');

class AccountingModel extends BaseModel
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';


    public function selectAllClientsCash($client_id)
    {
		$sql = "
		 select * from clients, points
		 where
			clients.client_id = points.client_id
		 and
			clients.client_id = {$client_id}
		";
		return $this->_db->fetchAll($sql);
    }

	public function selectPaymentsCash($client_id, $op_type)
	{
		$startM .= date('m')-1;
		$current_day = new Zend_Date(strtotime('now'));
		$startD = $current_day->toString('Y-MM-'). '01';
		$endD = date('Y-m-t'). " 23:59:59";

		$selectSql = "
			select
				*
			from
				transactions
			where
				client_id = {$client_id}
				and currenttime between '{$startD}' AND ('{$endD}'::timestamp - INTERVAL '1 seconds')
				and summa > 0
			order by
				currenttime
		";

		return $this->_db->fetchAll($selectSql);
	}

	public function startCorrect($summa, $client_id, $summa_dollar, $uid, $comment, $trantype)
	{
		$sql_up = "
			select buh_correct({$summa}, {$client_id}, {$summa_dollar}, {$uid}, '{$comment}', {$trantype});
		";
		return  $this->_db->fetchAll($sql_up);

	}

	public function pay($client_id, $point_id, $code, $summa, $servicetype, $commente)
	{
		$financeModel = new FinanceModel();

		$auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();


		$trantype = $financeModel->getPaymentFromCashDesks($code, $user->country);

		$currency = ClientHelper::getCurrencyByClientID($client_id);

		$sql = "
			select kassa_payments(
					{$client_id},
					{$point_id},
					{$trantype},
					{$summa},
					{$servicetype},
					$user->id,
					'{$commente}',
					'{$currency}'
			);
		";

		return $this->_db->fetchAll($sql);
	}

	public function showCorrect($client_id)
	{
		$sql = "
			select
				T.*,
				(select client_name from clients where client_id = T.client_id ) as client_name,
				(select ballance from clients where client_id = T.client_id ) as ballance
			from transactions as T
			where
				T.client_id = {$client_id}
			and
				T.trantype in (7117, 31)
			order by T.currenttime
		";
		return $this->_db->fetchAll($sql);
	}

	public function verifyStatusCrossAccounting($client_id)
	{
		$sql = "
		 select count(client_id) as verif from points where client_id = {$client_id} and statuscross >= 0
		";
		return $this->_db->fetchOne($sql);
	}
}
?>