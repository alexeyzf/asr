<?php
require_once('Zend/Db/Table.php');

class NoneactivModel extends Zend_Db_Table
{
    protected $_name = 'points';
    protected $_sequence = 'points_seq';
	
    public function getNoneActivClients($date)
	{
		/**
   		 *  Метод селектит список не активных клиентов
   		 */
		$sql = "
			SELECT
        		PTS.*,
        		ADSL.*,
        		CLA.*,
    			(SELECT 
    				dateagree 
    			FROM 
    				contracts 
    			WHERE 
    				client_id = CLA.client_id 
    				AND contract_type_id = 1 ) AS dateagree_contract,
    			(SELECT 
    				name 
    			FROM 
    				ats_list 
    			WHERE id = PTS.ats_id) AS ats_name
      		FROM 
      			clients AS CLA, 
      			adsl AS ADSL, 
      			points AS PTS
      		WHERE
        		CLA.client_id = PTS.client_id
      			AND PTS.point_id = ADSL.point_id
      			AND current_date between ADSL.startdate and ADSL.enddate
      			AND CLA.client_type_id = 1
      			AND ADSL.paidto is null
      			AND PTS.statuscross = 25
    	";
		
		if ($date)
		{
			$sql .= "
				AND (
					SELECT 
    					dateagree 
    				FROM 
 	   					contracts 
    				WHERE 
    					client_id = CLA.client_id 
    					AND contract_type_id = 1
    				) <= '{$date}'
			";
		}
		
		$sql .= "
			ORDER BY 
      			dateagree_contract DESC
		";
		
    	return $this->_db->fetchAll($sql);
  	}
}