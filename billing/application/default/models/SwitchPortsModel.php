<?php
/**
 * Model for switches_ports table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class SwitchPortsModel extends Zend_Db_table
{
	protected $_name = 'switches_ports';

	public function fillPorts($switchID, $count)
	{
		$sql = "
			SELECT
				count(id)
			FROM
				{$this->_name}
			WHERE
				switch_id = {$switchID}
		";

		$currentCount = $this->_db->fetchOne($sql);

		if ($count > $currentCount)
		{
			$data['switch_id'] = $switchID;
			$k = 1;

			for ($i = $currentCount; $i < $count; $i++)
			{
				$data['port_number'] = $k++;
				$this->insert($data);
			}
		}
	}

	public function savePort($data, $portID)
	{
		if($data['equipment_type'] == "transport")
		{
			$data['is_transport'] = "true";
			$data['look_forward'] = $data['switch_id'];
			unset($data['switch_id']);
		}

		$columns = $this->_getCols();

        foreach ($data as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($data[$key]);
            }
        }

		if ($portID)
		{
			$this->update($data, "id = {$portID}");
		}
		else
		{
			$portID = $this->insert($data);
		}

		return $portID;
	}

	public function getPort($portID)
	{
		$ports = $this->_db->select()->from($this->_name)
			->joinLeft('dslam_list', "dslam_list.id = {$this->_name}.dslam_id", array('ats_id'))
			->joinLeft('points', "points.point_id = {$this->_name}.point_id", array('pcross'))
			->joinLeft('clients', 'clients.client_id = points.client_id', array('client_name'))
			->where("{$this->_name}.id = ?", $portID)
			->query()->fetchAll();

		if ( $ports && is_array($ports) && is_array($ports[0]) )
		{
			return $ports[0];
		}
		else
		{
			return array();
		}
	}

	public function verifyIsTransportPort($portID)
	{
		$sql = "
			select
				SP.*,
				SP.id as switchbind_id,
				S.*,
				AL.*
			from switches_ports as SP, switches  as S, ats_list as AL
			where
					SP.is_transport  = true
			and
					SP.id = {$portID}
			and
					SP.look_forward = S.id
			and
					S.ats_id = AL.id limit 1
		";
		$resultPortData = $this->_db->fetchAll($sql);
		return $resultPortData;
	}

	public function getControlCenterMessages()
	{
		$sql = "
			select * from control_center
			where
				now() between date_action and is_posted
		";
		return $this->_db->fetchAll($sql);
	}

}