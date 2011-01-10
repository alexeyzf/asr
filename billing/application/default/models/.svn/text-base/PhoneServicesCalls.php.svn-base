<?php
/**
 * Model for phone_services_calls table
 *
 * @author marat
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class PhoneServicesCalls extends Zend_Db_Table_Abstract
{
	protected $_name = 'phone_services_calls';

	public function getNumbers($serviceType, $pointID)
	{
		if ($serviceType == 7030)
		{
			$numbers = $this->_db->select()->from('isdn_numbers', array('number'))
				->where("point_id = {$pointID}")
				->query()->fetchAll();
		}
		elseif ($serviceType == 7060)
		{
			$numbers = $this->_db->select()->from('tradtel_numbers', array('number'))
				->where("point_id = {$pointID}")
				->query()->fetchAll();
		}

		if ( ! $numbers )
		{
			return array();
		}
		else
		{
			$result = array();

			foreach ($numbers as $numberRecord)
			{
				array_push($result, $numberRecord['number']);
			}

			return $result;
		}
	}

	public function getStat($serviceType, $pointID, $startDate, $endDate, $number = '')
	{
		if ( ! $number )
		{
			$numbers = $this->getNumbers($serviceType, $pointID);
		}
		else
		{
			$numbers = array($number);
		}

		$sql = "
			SELECT
				phone_services_calls.call_date,
				phone_services_calls.call_time,
				phone_services_calls.abonent1,
				phone_services_calls.abonent2,
				round((talk_sec::float +29 ) / 60) ::float * phone_services_tarifs.price AS amount,
				round((talk_sec::float +29 ) / 60) ::float AS minutes_count
			FROM
				phone_services_calls, phone_services_tarifs
	  		WHERE
	  			phone_services_calls.call_date <= phone_services_tarifs.end_date
	  		AND
				phone_services_calls.call_date >= phone_services_tarifs.start_date
	  		AND
				talk_sec > 6 and ( abonent1=";

    	$sql .= implode(' or abonent1 = ', $numbers);
    	$sql .= " ) and abonent2::character varying ~ ('^' || phone_services_tarifs.prefix)
    				and call_date BETWEEN '{$startDate}' and '{$endDate}'
    		ORDER BY
    			phone_services_calls.abonent1, phone_services_calls.call_date
    	";

    	return $this->_db->fetchAll($sql);
	}

	public function getCalls($startDate, $endDate, $numbers = array())
	{
		$sql = "
			SELECT
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
				AND call_date BETWEEN '{$startDate}' and '{$endDate}'
		";

		if ( count($numbers) > 0 )
 		{
 			$sql .= 'AND (abonent1 = ' . implode(' or abonent1 = ', $numbers) . ')';
 		}

 		$sql .= " ORDER BY call_date, call_time ";

		return $this->_db->fetchAll($sql);
	}

	public function getAnalizNumbersReport($start, $end)
	{
		$services = array(
			'5000' => 'ngn',
			'7030' => 'isdn',
			'7060' => 'tradtel'
		);

		foreach($services as $key => $value)
		{

			$sql = "
				select
					CLA.client_id,
					CLA.client_name,
					(select sum(summa) from transactions
					where
						client_id = CLA.client_id
					and
						trantype = 1001
					and
						currenttime between '{$start}' and '{$end}'
					and servicetype = {$key}) as abon_pay,
					(select sum(summas) from transactions
					where
						client_id = CLA.client_id
					and
						trantype = 1001
					and
						currenttime between '2010-09-01 00:00:00' and '2010-09-30 23:59:59'
					and servicetype = 5000) as abon_pay_sum,
					(select sum(summa) from transactions
					where
						client_id = CLA.client_id
					and
						trantype = 7122
					and
						currenttime between '{$start}' and '{$end}' ) as peregovors,
					(select sum(summas) from transactions
					where
						client_id = CLA.client_id
					and
						trantype = 7122
					and
						currenttime between '{$start}' and '{$end}'  ) as peregovors_sum
				from clients as CLA, points as PTS, {$value} as NGN
				where
					CLA.client_id = PTS.client_id
				and
					PTS.point_id = NGN.point_id
				and
					'{$start}' >= startdate
				and
					'{$end}' <= enddate
				group by CLA.client_id, CLA.client_name
				order by CLA.client_name
			";
			$result[$value] = $this->_db->fetchAll($sql);
		}
		return $result;
	}

	public function getSearchByNumber($number)
	{
		$sql = "
			select
				*
			from phones_view as PV, points as PTS
			where
				PV.number = '{$number}'
			and
				PV.point_id = PTS.point_id
		";

		return $this->_db->fetchAll($sql);
	}

	public function getPhones($tablename, $isface)
	{
		$sql = "
			select
				*
			from phones_view as PV, points as PTS, clients as CLA
			where
				PV.tablename = '{$tablename}'
			and
				PV.point_id = PTS.point_id
			and
				PTS.client_id = CLA.client_id
			and
				CLA.client_type_id = {$isface}
			order by CLA.client_id
		";
		return $this->_db->fetchAll($sql);
	}

	public function getBusyNumbers()
	{
		$sql = "
			select number, PTS.u_login from phones_view as PV, points as PTS, clients as CLA
			where
				PV.point_id = PTS.point_id
			and
				PTS.client_id = CLA.client_id order by number
		";
		return $this->_db->fetchAll($sql);
	}

}
