<?php
/**
 * Model of phone_hub_list table
 *
 * @author marat
 */

 require_once('BaseModel.php');

class PhoneHubList extends BaseModel
{
    protected $_name = 'phone_hub_list';
    protected $_sequence = 'phone_hub_list_seq';

    public function getNotDeletedOptions()
    {
        $phoneHubList = $this->fetchAllNotDeleted();
        $phoneHubOptions = array();

        foreach ($phoneHubList as $phoneHub)
        {
            $phoneHubOptions[$phoneHub->id] = $phoneHub->name;
        }

        return $phoneHubOptions;
    }

    public function getAllDataHubs($start, $end)
    {
    	$sql = "
			select * from phone_hub_list order by id
		";
		$data = $this->_db->fetchAll($sql);

		$arr = array();

		foreach($data as $value)
		{
			$count_ports = $this->getCountCrossAndUncrossPortsByHubID($value['id'], $start, $end);
			$value['ports_statistic'] = $count_ports;
			array_push($arr, $value);
		}
		return $arr;
    }

    public function getCountCrossAndUncrossPortsByHubID($hubID, $start = null, $end = null)
    {
		$sql_uncrossed = "
			select
				count(id)
			from hub_registry
			where
				date_action between '{$start}' and '{$end}'
			and
				type_action = -25
			and
				hub_id  = {$hubID}
		";

		$ports_uncrossed = $this->_db->fetchOne($sql_uncrossed);



		$sql_crossed = "
			select
				count(id)
			from hub_registry
			where
				date_action between '{$start}' and '{$end}'
			and
				type_action = 25
			and
				hub_id  = {$hubID}
		";
		$ports_crossed = $this->_db->fetchOne($sql_crossed);

		$arr['crossed']   = $ports_crossed;
		$arr['uncrossed'] = $ports_uncrossed;


		return $arr;
    }

    public function addTransactionOnHub($hubID, $trantype, $summa, $notes)
    {
		$sql = "
			insert into hub_transactions (trantype, hub_id, summa, notes)
			values
			(
				{$trantype},
				{$hubID},
				{$summa},
				'{$notes}'
			)
		";

		$this->_db->fetchAll($sql);
    }

    public function getFinanceHistory($hub_id)
    {
    	$sql = "
			select * from hub_transactions where hub_id = {$hub_id} order by currenttime desc
		";

		return $this->_db->fetchAll($sql);
    }
}