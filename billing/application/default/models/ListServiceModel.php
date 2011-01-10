<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Zend/Db/Table.php');

class ListServiceModel extends Zend_Db_Table
{
    protected $_name = 'service_type';

	public function getPoints($client_id)
	{
	    $arrPointsSql = "
	    select PTS.point_id, CLA.client_type_id, PTS.statuscross from points as PTS, clients as CLA
	    where
	      PTS.client_id = CLA.client_id
	    and
	      CLA.client_id = {$client_id}
	    ";
	    return $this->_db->fetchAll($arrPointsSql);
	}

	public function updatePaidtoDate($service_id, $tablename, $point_id)
	{
		/**
		 *  Метод для корпов. Проставляет paidto дату после penable = true
		 */

		$next_month = date("Y-m-01", strtotime('+ 1 month'));

		$sql_bal = "
		select ballance from clients as CLA, points as PTS
		WHERE
			PTS.point_id = {$point_id}
		AND
			PTS.client_id = CLA.client_id
		LIMIT 1
		";
		$now_ballance = $this->_db->fetchOne($sql_bal);

		$sql = "
		update {$tablename} set paidto = '{$next_month}'
		where
			id = {$service_id}
		";

		$this->_db->fetchAll($sql);

		return $now_ballance;
	}

	public function getPaidToDateForCorp($sid, $tablename, $point_id)
	{
		$sql = "
		select paidto from {$tablename}
		where
			id = {$sid}
		and
			point_id = {$point_id}
		";
		return $this->_db->fetchOne($sql);
	}

    public function getAllService($faceid = '')
    {
        if($faceid == "")
        {
            $sql = "select * from service_type";
            return $this->_db->fetchAll($sql);
        }
        else
        {
            $sql = "
            select * from service_type
            where
            isface in ('{$faceid}',2)
            ";
            return $this->_db->fetchAll($sql);
        }
    }

    public function afterAddTarif($faceid)
    {
        $sql = "
        select TAR.* from service_type as ST, tarifs as TAR
            where
                ST.isface in ({$faceid},2)
            and
                ST.need_cross = 1
            and
                ST.servicetype_id = TAR.servicetype_id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getFace($client_id)
    {
        $sql = "
        select client_type_id from clients where client_id = {$client_id}
        ";
        return $this->_db->fetchOne($sql);
    }

    public function selectServices($point_id, $tablename)
    {
        $sql = "
        select
            LINK.*,
            PTS.connect_address as fiz_connect,
            (select u_login from points where
            point_id = LINK.point_id    ) as u_login,

            (select pcross from points where
            point_id = LINK.point_id) as pcross,

            (select short_name from service_type as ST, tarifs as TAR
            where
            ST.servicetype_id = TAR.servicetype_id
            and
            TAR.tarif_id = LINK.tarif_id
            ) as short_name,

            (select tarif_name from tarifs
            where tarif_id = LINK.tarif_id) as tarif_name

        from {$tablename} as LINK left join points as PTS
                            on(LINK.point_id = PTS.point_id)
            where
                LINK.point_id = '{$point_id}'
            and
                LINK.is_deleted = false
        ";
        return $this->_db->fetchAll($sql);
    }

    public function returnStatusNeedCross($service_id)
    {
        $sql = "
        	select need_cross from service_type where servicetype_id = {$service_id}
        ";
        return $this->_db->fetchOne($sql);
    }

    public function setServiceStartDate($tableLink, $pointID, $startDate)
    {
        $sql = "
            UPDATE
                {$tableLink}
            SET
                startdate = '{$startDate}'
            WHERE
                point_id = {$pointID}
                AND startdate = (
                    SELECT
                        MIN(startdate)
                    FROM
                        {$tableLink}
                    WHERE
                        point_id = {$pointID}
                    )
        ";

        $this->_db->fetchOne($sql);
    }

    public function setServiceEndDate($tableLink, $pointID, $endDate)
    {
        $sql = "
            UPDATE
                {$tableLink}
            SET
                enddate = '{$endDate}'
            WHERE
                point_id = {$pointID}
                AND startdate = (
                    SELECT
                        MAX(enddate)
                    FROM
                        {$tableLink}
                    WHERE
                        point_id = {$pointID}
                    )
        ";

        $this->_db->fetchOne($sql);
    }

    public function getCrossRegPay($pointID)
    {
        $tables = array(
            3000 => 'adsl',
            7000 => 'adsl',
            7100 => 'adsl',
            7110 => 'adsl',
            3100 => 'tasix',
            7020 => 'wifi',
            7030 => 'isdn',
            8000 => 'vpn'
        );

        foreach ($tables as $serviceType => $table)
        {
            $sql = "
                SELECT
                    {$table}.reg_pay
                FROM
                    {$table}
                JOIN
                    tarifs ON tarifs.tarif_id = {$table}.tarif_id
                WHERE
                    point_id = {$pointID}
                    AND tarifs.servicetype_id = {$serviceType}
            ";

            $regPay = $this->_db->fetchOne($sql);

            if ($regPay != 0)
            {
                return array($serviceType, $regPay);
            }
        }

        return null;
    }

}