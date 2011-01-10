<?php
/**
 * NetworkParamsModel
 *  
 * @author marat
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class NetworkParamsModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'network_params';
	
	public function updateAdmin($pointID, $serviceType, $adminID)
	{
		if ( ! $adminID )
		{
			$adminID = '0';
		}
		
		$data['admin_id'] = $adminID;
		$this->update($data, "point_id = {$pointID} AND service_type_id = {$serviceType}");
	}
}
