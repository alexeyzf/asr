<?php
/**
 * Model of tarif_components table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class TarifComponents extends Zend_Db_Table
{
    protected $_name = 'tarif_components';
    protected $_sequence = 'tarif_components_seq';

    public function getComponentsByTarifID($tarifID)
    {
        $tarifID = intval($tarifID);

        if (!$tarifID)
        {
            return array();
        }

        return $this->fetchAll('tarif_id = ' . $tarifID)->toArray();
    }
    
    public function getTrafficExcess($tarifID)
    {
    	$sql = "
    		SELECT
    			traffic_excess
    		FROM
    			tarif_components
    		WHERE
    			tarif_id = {$tarifID}
    		LIMIT 1
    	";
    	
    	return $this->_db->fetchOne($sql);
    }
}