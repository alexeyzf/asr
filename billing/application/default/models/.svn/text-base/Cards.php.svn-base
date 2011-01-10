<?php
/**
 * Model for cards table
 *
 *  @author marat
 */

require_once ('BaseModel.php');

class Cards extends BaseModel
{
	protected $_name = 'cards';

	public function search($serial, $number)
	{
		return $this->fetchAll("serial = '{$serial}' AND number = '{$number}'");
	}

	public function showFinanceHistory($client_id)
	{
		$sql = "
			select
				T.*,
                                (select typename from asrtypes where typename_id = T.trantype and typename_value = '13') as tran,
				(select client_name from clients where client_id = T.client_id) as client_name
			from transactions as T
                        where
                                T.client_id = {$client_id}
                        order by T.currenttime
		";
		return $this->_db->fetchAll($sql);
	}

	public function cardActivationDetails($serial, $number)
	{
		$sql = "
		select
				T.*,
				(select client_name from clients where client_id = T.client_id) as client_name,
				(select ballance from clients where client_id = T.client_id) as ballance,
				(select phone from clients where client_id = T.client_id) as phone
		from transactions as T
		where
		T.commente = (select pin from cards
						where
							serial = '{$serial}'
						and
							number = '{$number}'
					 )
		";

		return $this->_db->fetchAll($sql);
	}

	public function cardPeregovorDetails($serial, $number)
	{
		$sql = "
			select * from radacct where username = (select pin from cards where serial = '{$serial}' and number = '{$number}')
			order by acctstarttime desc
		";

		return $this->_db->fetchAll($sql);
	}
}