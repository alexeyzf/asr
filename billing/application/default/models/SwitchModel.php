<?php
/**
 * Model for switches table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class SwitchModel extends Zend_Db_Table
{
	protected $_name = 'switches';

	public function getList()
	{
		$switches = $this->fetchAll("is_deleted = false", "ip_address")->toArray();

		$switchesPortsData = $this->_db->select()->from('switches_ports')
			->joinLeft('dslam_list',
				'dslam_list.id = switches_ports.dslam_id',
				array('name AS dslam_name', 'ip_address AS dslam_ip'))
			->joinLeft('points',
				'points.point_id = switches_ports.point_id', array())
			->joinLeft('clients',
				'clients.client_id = points.client_id',
				array('client_name'))
			->order('port_number')
			->query()->fetchAll();
		$switchesPorts = array();

		foreach ($switchesPortsData as $row)
		{
			$switchesPorts[$row['switch_id']][] = $row;
		}

		foreach ($switches as $key => $switch)
		{
			$switches[$key]['ports'] = $switchesPorts[$switch['id']];
		}

		return $switches;
	}

	public function getSwitchByID($ID)
	{
		$sql = "
			SELECT
				*,
				(SELECT
					count(id)
				FROM
					switches_ports
				WHERE
					switches_ports.switch_id = {$this->_name}.id
				) AS ports_count
			FROM
				{$this->_name}
			WHERE
				id = {$ID}
		";

		return $this->_db->fetchRow($sql);
	}

	public function saveChanges($data, $ID = null)
	{
		$columns = $this->_getCols();

        foreach ($data as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($data[$key]);
            }
        }

        unset($data['id']);

		if ($ID)
		{
			$this->update($data, "id = {$ID}");
		}
		else
		{
			$ID = $this->insert($data);
		}

		return $ID;
	}

	public function deleteSW($ID)
	{
		$sql_delete_switch = "
			delete from switches where id = {$ID}
		";
		$result = $this->_db->fetchAll($sql_delete_switch);
		return $result;
	}

    public function getOptionsForSwitch($where)
    {
        $switchList = $this->fetchAll($where, 'ip_address');
        $switchOptions = array();

        foreach ($switchList as $switch)
        {
            $switchOptions[$switch->id] = $switch->ip_address . ' (' . $switch->ip_address . ')';
        }

        return $switchOptions;
    }

    public function clearTransportPortByID($ID)
    {
    	$sql = "
			update switches_ports set is_transport = false, look_forward = null
			where id = {$ID}
		";
		return $this->_db->fetchAll($sql);
    }

}