<?php


require_once('Zend/Db/Table.php');

class ServiceType extends Zend_Db_Table
{
	public function fetchOptions()
	{
		$sql = "
			SELECT
				servicetype_id,
				short_name
			FROM
				service_type
		";
		
		return $this->_db->fetchPairs($sql);
	}
	
	public function getServiceType($table, $serviceID)
	{
		$sql = "
			SELECT
				service_type.servicetype_id
			FROM
				service_type
			JOIN
				tarifs ON tarifs.servicetype_id = service_type.servicetype_id
			JOIN
				{$table} ON {$table}.tarif_id = tarifs.tarif_id
			WHERE
				{$table}.id = {$serviceID}
		";
				
		return $this->_db->fetchOne($sql);
	}
	
	public function getServicesTables()
    {
    	$sql = "
    		SELECT
    			servicetype_id AS t1,
    			tablelink AS t2
    		FROM
    			service_type
			WHERE
				tablelink is not null
	    	ORDER BY 
	    		tablelink DESC, servicetype_id
    	";

    	return $this->_db->fetchPairs($sql);
    }
    
    public function getServiceTypeID($tableLink, $pointID)
    {
    	$sql = $this->_db->select()
    		->from($tableLink, array())
    		->join('tarifs', "tarifs.tarif_id = {$tableLink}.tarif_id", array('servicetype_id'))
    		->where('point_id = ?', $pointID)
    		->order("{$tableLink}.startdate DESC");
    		
    	return $this->_db->fetchOne($sql);
    }
}