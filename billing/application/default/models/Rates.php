<?php
/**
 * Model for rates table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class Rates extends Zend_Db_Table
{
	protected $_name = 'rates';

	public function getRate($date)
	{
		$sql = "
			SELECT
				rate
			FROM
				rates
			WHERE
				'{$date}' BETWEEN startdate AND enddate
			ORDER BY
				startdate DESC
		";

		return $this->_db->fetchOne($sql);
	}


	public function getRateNow()
	{
		$sql = "
			SELECT
				rate
			FROM
				rates
			WHERE
				now() BETWEEN startdate AND enddate
			ORDER BY
				startdate DESC
		";

		return $this->_db->fetchOne($sql);
	}
}