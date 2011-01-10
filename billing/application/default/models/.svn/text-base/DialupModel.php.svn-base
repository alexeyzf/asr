<?php
/**
 * Model for dialup table
 *
 * @author marat
 */

class DialupModel extends BaseModel
{
    protected $_name = 'dialup';
    protected $_sequence = 'dialup_seq';

    public function getSessionSumForDialup($username, $startdate, $enddate)
    {
    	$startdate = $startdate . " 00:00:00";
    	$enddate   = $enddate . " 23:59:59";

		$sql = "
			select sum(acctsessiontime)/3600 as hours
			from radacct
			where
				username = '{$username}'
			and
				acctstarttime between '{$startdate}' and '{$enddate}'
		";
		return $this->_db->fetchOne($sql);
    }
}