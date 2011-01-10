<?php
require_once('Zend/Db/Table.php');

class CompareModel extends Zend_Db_Table
{
    protected $_name = 'ports';

	public function getAllPorts()
	{
		$sql = "
			select
				PRT.*,
				PTS.*,
				CLA.*,
				CS.speed
			from points as PTS, ports as PRT, clients as CLA, client_services as CS
			where
				PTS.port_id = PRT.id
			and
				PTS.client_id = CLA.client_id
			and
				PTS.point_id = CS.point_id
			and
				CLA.client_type_id = 0
            and
                    CLA.is_employee = false
            and
                    CLA.is_donate = false
            and
                    CLA.vip = false
		";
		return $this->_db->fetchAll($sql);
	}

        public function getTrafficDayly($pid)
        {
            $corpTrafficModel = new CorpTraffic();

            $start = date('Y-m-d'). " 00:00:00";
            $end   = date('Y-m-d'). " 23:59:59";

            $data      = array();

            $ip = $corpTrafficModel->getArrayIP($pid);

                    for($i = 0; $i < count($ip); $i++)
                    {
                        if($flag == 1)
                        {
                                array_push($data,$this->getDataTraffic($ip[$i], $start, $end));
                        }
                        else
                        {
                                array_push($data,$this->getDataTraffic($ip[$i], $start, $end));
                        }
                    }

                    for($i = 0; $i < count($data); $i++)
                    {
                        $bytes_in  = $bytes_in + $data[$i][0]['bin'];
                        $bytes_out = $bytes_out + $data[$i][0]['bout'];
                    }


            if ($bytes_in < 3 * $bytes_out)
            {
                $total = $bytes_out - $bytes_in / 3 + $bytes_in;
            }
            else
            {
                $total = $bytes_in;
            }

            $data['bytes_in']  = $bytes_in / 1024 / 1024;
            $data['bytes_out'] = $bytes_out / 1024 / 1024;
            $data['total']     = $total / 1024 / 1024;

            return $data;
        }

        public function getDataTraffic($ip, $start, $end)
        {
            $sql = "
                select sum(bytes_in) as bin, sum(bytes_in) as bout
                from data01
                where
                    ip_address = '{$ip}'
                and
                    direction_num = 28910
                and
                    date_time between '{$start}' and '{$end}'
            ";
            return $this->_db->fetchAll($sql);
        }

        public function getLastPortSpeed($clientID, $dslamid, $port)
        {
        	$sql = "
				select
					PRT.speed as speed_last
				from porttasks as PRT
				where
					datedone = (select max(datedone) from porttasks where port_clientid  = {$clientID}
                                                    and dslamid = {$dslamid} and portnumber = {$port} and tasktype = 0)
			";
			return $this->_db->fetchOne($sql);
        }
}

?>
