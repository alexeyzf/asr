<?php
/**
 * Model for radreply table
 * 
 * @author marat
 */

require_once('Zend/Db/Table.php');

class Radreply extends Zend_Db_Table
{
    protected $_name = 'radreply';
    
    const SESSION_TIMEOUT_ATTRIBUTE = 'Session-Timeout';
    const SESSION_OCTETS_LIMIT = 'Session-Octets-Limit'; 
	
    public function deleteUser($login, $serviceType)
    {
		$this->delete("username = '{$login}' 
			AND servicetype = {$serviceType}");
    }
    
	public function deleteAttributeValue($login, $serviceType, $attribute)
	{
		$this->delete("username = '{$login}' 
			AND attribute = '{$attribute}'
			AND servicetype = {$serviceType}");
	}
	
	public function getAttributes($login, $serviceType)
	{
		return $this->fetchAll("username = '{$login}' AND servicetype = {$serviceType}");
	}
	
	public function setLimit($userName, $serviceType)
	{
		$sql = "
			SELECT
				setlimit('{$userName}', $serviceType::smallint, '')
		";
		
		$result = $this->_db->fetchOne($sql);
		return $result;
	}
}