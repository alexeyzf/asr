<?php
/**
 * Модель для работы стаблицей, сохраняющей 
 * валютные ставки для казначейства
 * 
 * @author marat
 */

require_once ('Zend/Db/Table/Abstract.php');

class TreasuryRateModel extends Zend_Db_Table_Abstract 
{
	protected $_name = 'treasury_rates';
	
	public function getCurrentRate()
	{
		$sql = "
			SELECT
				rate
			FROM
				{$this->_name}
			WHERE
				current_date >= startdate 
			ORDER BY
				startdate desc
			LIMIT 1
		";
		
		return $this->_db->fetchOne($sql);
	}
	
	public function getRate($date)
	{
		$sql = "
			SELECT
				rate
			FROM
				{$this->_name}
			WHERE
				'{$date}' >= startdate 
			ORDER BY
				startdate desc
			LIMIT 1
		";
		
		return $this->_db->fetchOne($sql);
	}
}