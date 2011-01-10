<?php
/**
 * Model for switches_types table
 *  
 * @author marat
 * @version 
 */

require_once('Zend/Db/Table/Abstract.php');

class SwitchesTypesModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'switches_types';
	
	/**
	 * Sequence name
	 */
	protected $_sequence = 'switches_types_seq';
	
	public function getOptions()
	{
		$sql = "
			SELECT
				id,
				name
			FROM
				{$this->_name}
		";
		
		return $this->_db->fetchPairs($sql);
	}
	
	public function getByID($ID)
	{
		$record = $this->fetchRow("id = {$ID}");
		
		if ( ! $record )
		{
			return array();
		}
		else
		{
			return $record->toArray();
		}
	}
	
	public function add($name, $portsCount)
	{
		$data['name'] = $name;
		$data['ports_count'] = $portsCount;
		
		return $this->insert($data);
	}
}
