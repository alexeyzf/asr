<?php
/**
 * Model of radacct table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class Radacct extends Zend_Db_Table
{
    protected $_name = 'radacct';
    protected $_sequence = 'radacct_seq';

    public function getTopSessions($login, $count = 50)
    {
       return $this->fetchAll("username = '{$login}'", "acctstarttime DESC", $count)->toArray();
    }

    public function closeSession($radactID)
    {
        if ( ! $radactID )
        {
            return false;
        }

        $data['acctstoptime'] = new Zend_Db_Expr("now()");
        //$this->update($data, "radacctid = {$radactID}");
        $sql = "
		update radacct
			set acctstoptime = now(), acctstopdelay = 0  , acctterminatecause = 'User-Request',
			acctstartdelay = 0
		where radacctid = {$radactID}
		";
		$this->_db->fetchAll($sql);
    }

    public function deleteSession($radactID)
    {
        if ( ! $radactID )
        {
            return false;
        }

        $this->delete("radacctid = {$radactID}");
    }

    public function getTraffic($login, $startDate, $endDate)
    {
        $sql = "
            SELECT
            	acctstarttime,
            	acctstoptime,
                nas.servicetype AS servicetype,
                acctinputoctets::real  / 1024 / 1024 AS output,
                acctoutputoctets::real /1024 / 1024 AS input
            FROM
                radacct
            LEFT JOIN
                nas on nas.ipaddr = radacct.nasipaddress
            WHERE
                username = '{$login}'
                AND acctstarttime BETWEEN '{$startDate}' AND '{$endDate}'
            ORDER BY
            	nas.servicetype, acctstarttime
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getTrafficHistory($clientID, $startDate, $endDate)
    {
        $sql = "
            SELECT
                username,
                acctstarttime AS session_time,
                acctinputoctets::real / 1024 / 1024 AS input,
                acctoutputoctets::real / 1024 / 1024 as output
            FROM
                radacct
            WHERE
                username = (
                    SELECT
                        u_login
                    FROM
                        points
                    WHERE
                        client_id = {$clientID}
                )
                AND acctstarttime > '{$startDate}'
                AND acctstarttime < '{$endDate}'
            ORDER BY username, session_time
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getTrafficStats($pointID, $startDate, $endDate)
    {
    	 $sql = "
            SELECT
                date_trunc('day', acctstarttime) AS session_time,
                sum(acctinputoctets::real) / 1024 / 1024 AS input,
                sum(acctoutputoctets::real) / 1024 / 1024 as output
            FROM
                radacct
            WHERE
                username = (
                    SELECT
                        u_login
                    FROM
                        points
                    WHERE
                        point_id = {$pointID}
                )
                AND acctstarttime > '{$startDate}'
                AND acctstarttime < '{$endDate}'
            GROUP BY
            	session_time
            ORDER BY session_time
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getStreamTraffic($pid, $start = null, $end = null)
    {

    	$sql = "
		select
			ST.date,
			ST.traffic_in / 1024 / 1024 as in,
			ST.traffic_out / 1024 /1024 as out,
			ST.username,
			ST.id
		from stream_traffic as ST
		where
			ST.date between '{$start}' and '{$end}'
		and
			ST.username = (select u_login from points where point_id = {$pid})
		";
		$data = $this->_db->fetchAll($sql);

		return $data;
    }
}
