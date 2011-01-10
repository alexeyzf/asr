<?php
require_once ('BaseModel.php');

class CorpTraffic extends BaseModel
{
    protected $_name = 'direction_control';
    protected $_sequence = 'direction_control_seq';


    public function showStat($point_id, $start, $end, $flag = null)
    {

    	if($flag == 1)
    	{
    		$ip = $point_id;
    	}
    	else
    	{
    		$ip = $this->getIP($point_id);
    	}


        if ( ! $ip )
        {
        	return array();
        }

        $ip = '\'' . str_replace(' ', '\' , \'', $ip) . '\'';

        $sql = "
        	SELECT
        		ip_address,
        		date_trunc('day', date_time) AS date_time,
        		MIN(date_time) AS start_time,
        		MAX(date_time) AS end_time,
        		SUM(bytes_in)::real / 1024 / 1024 AS bytes_in,
        		SUM(bytes_out)::real / 1024 / 1024 AS bytes_out
        	FROM
        		data01
        	WHERE
        		date_time BETWEEN '{$start}' AND '{$end}'
        		AND ip_address IN ({$ip})
        	GROUP BY
        		ip_address, date_trunc('day', date_time)
        	ORDER BY
        		date_time
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getIP($pid)
    {
    	$pointIPAddressModel = new PointIpAddresses();
    	$ips = $pointIPAddressModel->getPointIpAddresses($pid);
    	return implode(' ', $ips);
    }

    public function getRows($ip, $s_date, $e_date)
    {
    	$rows = array();

    	$ipArray = explode(' ', $ip);

    	foreach($ipArray as $key => $value)
    	{

    		$sql = "
	            select * from data01
	            where
	                ip_address = '{$value}'
				and
					date_time between '{$s_date}' and '{$e_date}'
	            order by date_time desc
        	";
			array_push($rows, $this->_db->fetchAll($sql));
    	}
		return $rows;
    }
    
    public function getRowsForDraw($ip, $startDate, $endDate)
    {
    	$sql = "
			SELECT 
				date_trunc('minute', date_time) AS date_time,
				SUM(bytes_in) AS input,
				SUM(bytes_out) AS output
			FROM 
				data01
			WHERE
				ip_address = '{$ip}'
				AND date_time between '{$startDate}' and '{$endDate}'
			GROUP BY
				date_trunc('minute', date_time)
	        ORDER BY
	        	date_time
	        
        	";
    	return $this->_db->fetchAll($sql);
    }

    public function getTotalBytes($pid, $start, $end, $flag = null, $flag_arhiv = null)
    {

		$data      = array();

		if($flag_arhiv == 1)
		{
			$ip = $pid;
		}
		else
		{
        	$ip = $this->getArrayIP($pid);
		}


		for($i = 0; $i < count($ip); $i++)
		{
			if($flag == 1)
			{
				array_push($data,$this->bytesCollector($ip[$i], $start, $end, 1));
			}
			else
			{
				array_push($data,$this->bytesCollector($ip[$i], $start, $end));
			}
		}

		for($i = 0; $i < count($data); $i++)
		{
			$bytes_in  = $bytes_in + $data[$i][0]['sum_in'];
			$bytes_out = $bytes_out + $data[$i][0]['sum_out'];
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

	public function bytesCollector($ip,$start, $end, $flagBytes = null)
	{
		$sql = "
			select sum(traffic_in) as sum_in, sum(traffic_out) as sum_out
			from direction_control
			where
				ipaddress = '{$ip}'
			and
				to_char(currenttime,'YYYY-MM') = to_char('{$start}'::date, 'YYYY-MM')
        ";

        if($flagBytes == 1)
        {
        	$sql .= "
				and direction = 28910
			";
        }

        return $this->_db->fetchAll($sql);
	}


	public function getArrayIP($pointID)
	{
		$sql = "
        	SELECT
        		ip_address
        	FROM
        		point_ip_addresses
        	WHERE
        		point_id = {$pointID}
        		AND now() BETWEEN start_date AND end_date
        ";

        return $this->_db->fetchCol($sql);
	}


}