<?php

/**
 * MysqlTraffic
 *
 * @author marat
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class MysqlTraffic extends Zend_Db_Table_Abstract
{
	protected $_name = 'Users';

	public function getCorpTableName($pointID)
    {
        $sql = "
            SELECT
                Users.TableName
            FROM
                Users
            JOIN
                Services ON Services.mClientID = Users.ID
            WHERE
                Services.pcLientID = {$pointID}
        ";

        return $this->_db->fetchOne($sql);
    }

	public function getTraffic($tableName, $startDate, $endDate)
    {
        $sql = "
            SELECT
            	DATE(TekTime) AS session_time,
                sum(TraficIn) / 1024 / 1024 AS input,
                sum(TraficOut) / 1024 / 1024 AS output
            FROM
                {$tableName}
            WHERE
                TekTime < '{$endDate}'
                AND TekTime >= '{$startDate}'
            GROUP BY
            	session_time
            ORDER BY
             	session_time
        ";
		print $sql;
        return $this->_db->fetchAll($sql);
    }

    public function createTableCorp($login)
    {
    	$sql = "
		 CREATE TABLE {$login}
			(
				TekTime datetime,
				TraficIn int(10),
    			TraficOut int(10)
			);
		";
		$this->_db->fetchAll($sql);
    }


}
