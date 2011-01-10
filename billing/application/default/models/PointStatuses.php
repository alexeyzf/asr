<?php
/**
 * Model of point_statuses_view view
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');
require_once('PointHelper.php');

class PointStatuses extends Zend_Db_Table
{
    protected $_name = 'point_statuses_view';
    protected $_primary = 'code';

    const TYPE_EMPTY = 0;
    const TYPE_PROBLEM = 1;
    const TYPE_PROBLEM_SOLVED = 2;

    /**
     * Gets status label by code
     *
     * @param string $code - Code
     * @return string Label
     */
    public function getLabelByCode($code)
    {
        $row = $this->fetchRow("code = {$code}");
        return $row->label;
    }

    /**
     * Gets options for select
     *
     * @param string $where - Filter
     * @return array - code => value
     */
    public function getOptions($where = '')
    {
        $statuses = $this->fetchAll($where);
        $options = array();

        foreach($statuses as $status)
        {
            $options[$status->code] = $status->label;
        }

        return $options;
    }

    public function getCrossOptions()
    {
        $status = PointHelper::STATUS_CROSS_TO_CROSS;
        return $this->getOptions("code >= {$status} AND code != " . PointHelper::STATUS_CROSS_CROSS_DONE);
    }

    public function getCrossDoneStatus()
    {
        return $this->fetchRow('code = '. PointHelper::STATUS_CROSS_CROSS_DONE);
    }

    public function getUncrossOptions()
    {
        $status = PointHelper::STATUS_CROSS_TO_UNCROSS;
        return $this->getOptions("code <= {$status} AND code != " . PointHelper::STATUS_CROSS_UNCROSS_DONE);
    }

    public function getUncrossDoneStatus()
    {
        return $this->fetchRow('code = '. PointHelper::STATUS_CROSS_UNCROSS_DONE);
    }


	public function getProblemClients()
	{
		$sql = "
			select
				PTS.*,
				CLA.*,
				(select name from ats_list
				 where id = PTS.ats_id) as ats_name,

				(select phone_hub_list.name from ats_list, phone_hub_list
				where
					ats_list.phone_hub_id = phone_hub_list.id
				and
					ats_list.id = PTS.ats_id) as ph_name,

				(select phone_hub_list.id from ats_list, phone_hub_list
				where
					ats_list.phone_hub_id = phone_hub_list.id
				and
					ats_list.id = PTS.ats_id) as hub_id,

				(select label from point_statuses_view
				 where code = PTS.statuscross) as label
			from
				clients as CLA, points as PTS
			where
				CLA.client_id = PTS.client_id
			and
				PTS.statuscross in (select code from point_statuses_view where type = 1)
			order by hub_id
		";
		return $this->_db->fetchAll($sql);
	}


    public static function getProblemResolvedStatus($problemStatus)
    {
        switch ($problemStatus)
        {
            case PointHelper::STATUS_CROSS_BLOCKER:
                return PointHelper::STATUS_CROSS_BLOCKER_FIXED;

            case PointHelper::STATUS_CROSS_DEBT:
                return PointHelper::STATUS_CROSS_DEBT_REPAY;

            case PointHelper::STATUS_CROSS_INVALID_LAW_ADDRESS:
                return PointHelper::STATUS_CROSS_LAW_ADDRESS_FIXED;

            case PointHelper::STATUS_CROSS_INVALID_OWNER:
                return PointHelper::STATUS_CROSS_OWNER_FIXED;

            case PointHelper::STATUS_CROSS_OTHER_ISP:
                return PointHelper::STATUS_CROSS_OTHER_ISP_FIXED;
        }
    }

    public function getUncrossClients()
    {
     	$sql = "
			select
				PTS.*,
				(select client_name from clients where client_id = PTS.client_id limit 1) as client_name,
				(select ballance from clients where client_id = PTS.client_id limit 1) as ballance
			from points as PTS
			where PTS.statuscross = -25
		";
    }

    public function writeHubStatistic($atsID, $action)
    {
    	$sql_hub = "
			select phone_hub_id from ats_list where id = {$atsID}
		";
		$hubID = $this->_db->fetchOne($sql_hub);

		if($hubID)
		{
			$sql_insert = "
				insert into hub_registry (type_action, hub_id)
				values
				(
					{$action},
					{$hubID}
				)
			";
			$this->_db->fetchAll($sql_insert);
			return 1;
		}
		else
		{
			return 0;
		}
    }

}
