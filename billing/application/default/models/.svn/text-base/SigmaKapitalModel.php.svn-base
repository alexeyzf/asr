<?php
require_once('Zend/Db/Table.php');

class SigmaKapitalModel extends Zend_Db_Table
{
        protected $_name     = 'sigma_secret_serial';
        protected $_sequence = 'sigma_secret_serial_seq';

	public function getSigmaSerial()
	{
		$sql = "
			select * from sigma_secret_serial
                        where
                            now() between startdate and enddate - INTERVAL '1 second'
		";
		return $this->_db->fetchAll($sql);
	}

	public function setSigmaSerial($newSerial, $newPercent, $start, $end, $uid)
	{
                //$this->closeActivSigmaSerial();
		$sql = "
                    insert into sigma_secret_serial (last_change, startdate, enddate, serial, percent, userid) values
                    (
                        now(),
                        '{$start}',
                        '{$end}',
                        '{$newSerial}',
                        '{$newPercent}',
                        {$uid}
                    )
		";
		$this->_db->fetchAll($sql);
	}

        public function closeSigmaSerial($serial)
        {
            $sql = "
                update sigma_secret_serial set enddate = now()
                where
                    serial = '{$serial}'
                and
                    now() between startdate and enddate - INTERVAL '1 second'
            ";
            $this->_db->fetchAll($sql);
        }

        public function verifyNonActivCardsSerial($serial)
        {
            $sql = "
                select number from cards where serial = '{$serial}'
                and
                    is_activated = false
            ";

            return $this->_db->fetchAll($sql);
        }


        public function getStatisticSigma($start, $end)
        {
        	$sql = "
				select
					T.*,
					(select typename from asrtypes where typename_value = '22' and typename_desc::character varying = substring(CRD.serial from 1 for 2)) as filial,
					(select typename_desc from asrtypes where typename_value = '22' and typename_desc::character varying = substring(CRD.serial from 1 for 2)) as filial_cod,
					(select client_name from clients where client_id = T.client_id) as client_name,
					(select ballance from clients where client_id = T.client_id) as ballance
				from transactions as T, cards as CRD
				where
					T.commente = CRD.pin
				and
					CRD.with_bonus = true
				and
					currenttime between '{$start}' and '{$end}'
				order by client_name
			";

			$data = $this->_db->fetchAll($sql);

			foreach($data as $key => $value)
			{
				if($value['filial_cod'] == "")
				{
					 $value['filial'] = "ОПЕРУ";
					 $arr['operu'][] = $value;
				}
				else
				{
					$arr[$value['filial_cod']][] = $value;
				}
			}
			return $arr;
        }

        public function groupingCash($start, $end)
        {
        	$sql=  "
				select
					sum(T.summas) as cash,
					summas
				from transactions as T, cards as CRD
				where
					T.commente = CRD.pin
				and
					CRD.with_bonus = true
				and
					currenttime between '{$start}' and '{$end}'
				group by T.summas, summas
			";

			return $this->_db->fetchAll($sql);

        }

}