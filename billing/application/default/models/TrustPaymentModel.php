<?php

require_once 'Zend/Db/Table/Abstract.php';

class TrustPaymentModel extends Zend_Db_Table_Abstract
{
    protected $_name     = 'stream_additional_services';
    protected $_sequence = 'stream_additional_services_seq';


    public function trustPaymentSql($username)
    {

        $now = date('Y-m-d h:m:s');

        $end = date('Y-m-d h:m:s', strtotime('+2 days'));


        $sql = "
            select * from trust_payment_sql('{$username}', '{$now}', '{$end}')
        ";
        return $this->_db->fetchRow($sql);
    }

    public function getTrustPayment($username, $flag = false)
    {
    	if($flag)
    	{
    		$sql = "select * from trust_payments where username = '{$username}' order by enddate";

    		return $this->_db->fetchAll($sql);
    	}
    	else
    	{
    		$sql = "
				select * from trust_payments where username = '{$username}'
				and
					now() between startdate and enddate
			";
			return $this->_db->fetchRow($sql);
    	}


    }

}
