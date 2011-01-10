<?php
/**
 * Model for table schets
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class Schets extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'schets';
	
	public function getData($clientID, $month, $year)
	{
		$lastDate = date('Y-m-d', strtotime("01.{$month}.{$year}"));
		
		$sql = "
			SELECT
				schets.*,
				service_type.servicetype_name
			FROM
				schets
			LEFT JOIN
				service_type ON service_type.servicetype_id = schets.servicetype_id
			WHERE
				client_id = {$clientID}
				AND lastdate = '{$lastDate}'
		";
		return $this->_db->fetchAll($sql);
	}

        public function getNewSchet($client_id, $month, $year)
        {
            $lastDate = date('Y-m-d', strtotime("01.{$month}.{$year}"));

            $schetNumber = date('y', strtotime($lastDate)). "/". date('m', strtotime($lastDate));

            $sql = "
                select
                        CS.client_id,
                        CS.servicetype_id,
                        CS.tarif_price as amount,
                        '{$lastDate}'::timestamp as lastdate,
                        '{$schetNumber}'::character varying as number,
                        dollar2sum(CS.tarif_price) as amount_uzs,
                        (select servicetype_name from service_type where servicetype_id = CS.servicetype_id) as servicetype_name
                from client_services as CS
                where
                        client_id = {$client_id}
            ";
            return $this->_db->fetchAll($sql);
        }
}
