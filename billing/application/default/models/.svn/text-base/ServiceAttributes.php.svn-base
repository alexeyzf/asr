<?php
/**
 * Model of service_attributes table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class ServiceAttributes extends Zend_Db_Table
{
    protected $_name = 'service_attributes';
    protected $_sequence = 'service_attributes_seq';


	public function saveIP( $cl_data ,$data, $tablename)
	{

		/**
		 *  Метод аддон
		 */

		$stack = array();
		$arrIPs = explode(',',$data['ip_address']);

		for($i = 0; $i < count($arrIPs); $i++)
		{
			$test['clientid'] 		= $cl_data['data']['client_id'];
			$test['sid']	   		= $cl_data['data']['service_id'];
			$test['servicetype_id']	= $cl_data['data']['servicetype_id'];
			$test['address']		= $arrIPs[$i];
			array_push($stack, $test);
		}

		return $stack;
	}

    public function getList($where = '', $order = '')
	{
		$sql = "
			SELECT
                client_services.point_id,
                client_services.client_name,
                client_services.u_login,
                client_services.pcross,
                client_services.servicetype_id,
                client_services.tablename AS tablelink,
                client_services.admin_id AS admin_id,
                client_services.speed AS tarif_speed,
                tarifs.tarif_name,
                ats_list.name AS ats,
                client_services.dslam_ip,
                client_services.portnumber,
                ports.frame_number,
                ports.state AS port_state,
                service_type.servicetype_name,
                service_type.need_cross,
                client_services.service_id,
                dslam_list.name AS dslam_name,
                (SELECT
                	typename
				 FROM
				 	asrtypes
				 WHERE
					typename_value = '5'
				 	AND typename_id = dslam_list.type_id) AS dslamtype1,
                users.first_name || ' ' || users.last_name AS admin
            FROM
                client_services
            LEFT JOIN
                users ON users.id = client_services.admin_id
            LEFT JOIN
                ats_list ON ats_list.id = client_services.ats_id
            LEFT JOIN
            	dslam_list ON dslam_list.id = client_services.dslam_id
            LEFT JOIN
            	ports ON ports.id = client_services.port_id
            LEFT JOIN
            	tarifs ON tarifs.tarif_id = client_services.tarif_id
            JOIN
                service_type ON service_type.servicetype_id = client_services.servicetype_id
        ";


        if ($where)
        {
            $sql .= " WHERE  {$where} ";
        }

        if ($order)
        {
            $sql .= " ORDER BY {$order} ";
        }

        return $this->_db->fetchAll($sql);
    }

    public function getNotSetList($order = '', $flag)
    {
    	if($flag == 1)
    	{
    		return $this->getList(NULL, $order);
    	}
    	else
    	{
    		$filterServices = array(1000, 1100, 1200, 3000, 7040, 7070, 9999);

    		return $this->getList('(admin_id IS NULL OR admin_id = 0)
    			AND client_services.statuscross = 25
    			AND client_services.servicetype_id not in (' . implode(',', $filterServices) . ')', $order);
    	}

    }

    public function search($param, $value)
    {
        if ($param == 'login')
        {
            $param = 'u_login';
        }
        elseif ($param == 'name')
        {
            $param = 'client_name';
        }
        elseif ($param == 'phone')
        {
            $param = 'pcross';
        }
        elseif ($param == 'ats')
        {
            $param ='ats_list.name';
        }
        elseif ($param == 'dslam')
        {
            $param = 'dslam_ip';
        }

        return $this->getList("{$param} LIKE '%{$value}%' AND client_services.servicetype_id <> 9999 ");
    }

    public function getInfo($pointID, $serviceType = null)
    {
    	$sql = "
            SELECT
                client_services.client_name,
				client_services.client_id,
                client_services.u_login,
                client_services.pcross,
                client_services.tarif_id,
                client_services.tablename as tablelink,
                ats_list.name AS ats,
                client_services.dslam_ip,
                client_services.servicetype_id,
                service_type.servicetype_name,
                client_services.service_id,
				client_services.point_id
            FROM
                client_services
            LEFT JOIN
                ats_list ON ats_list.id = client_services.ats_id
            JOIN
                service_type ON service_type.servicetype_id = client_services.servicetype_id
            WHERE
                client_services.point_id = {$pointID}
		";

    	if ($serviceType)
    	{
    		$sql .= " AND client_services.servicetype_id = {$serviceType} ";
    	}

        $data = $this->_db->fetchRow($sql);
		$result['data'] = $data;
		$result['attributes'] = array();

        $attributes = $this->fetchAll("service_type_id = {$data['servicetype_id']}")->toArray();

        if ( count($attributes) == 0 )
        {
            return $result;
        }

        $sql = "
            SELECT
                *
            FROM
                network_params
            WHERE
                point_id = {$pointID}
        ";

    	if ($serviceType)
    	{
    		$sql .= " AND service_type_id = {$serviceType} ";
    	}

        $attrValues = $this->_db->fetchRow($sql);
        $counter = 0;

        foreach ($attributes as $attr)
        {
            $result['attributes'][$counter]['column_name'] = $attr['column_name'];
            $result['attributes'][$counter]['name'] = $attr['name'];
            $result['attributes'][$counter]['type'] = $attr['id'];
            $result['attributes'][$counter]['value'] = $attrValues[$attr['column_name']];
            $counter++;
        }

        return $result;
    }

    public function updateAttributes($ID, $data)
    {
    	$counter = $this->getPID($ID, $data['service_type_id']);

		if($counter == 0)
		{
			// Если все еще нет таких параметров у точки то инсерт иначе апдейт
			$data['point_id'] = $ID;
			$this->_db->insert('network_params', $data);
		}
		else
		{
			// апдейт
			$this->_db->update('network_params',
				$data,
				"point_id = {$ID} AND service_type_id = {$data['service_type_id']}");
		}
    }

	// ADDON
    public function getPID($pointID, $serviceTypeID = 7000)
    {
    	$sql = "
			SELECT
				count(point_id) as counter
			FROM
				network_params
			WHERE
				point_id = {$pointID}
				AND service_type_id = {$serviceTypeID}
		";

		return $this->_db->fetchOne($sql);
    }
	// END ADDON

    public function updatePointService($tarifID, $pointID, $userID)
    {
        $sql = "
            SELECT
                tablelink
            FROM
                tarifs
            WHERE
                tarif_id = {$tarifID}
        ";

        $table = $this->_db->fetchOne($sql);
        $data['admin_id'] = $userID;
        $this->_db->update($table, $data, "point_id = {$pointID}
        									AND now() BETWEEN startdate AND enddate");
    }


	public function listClients()
	{
		$sql = "
			SELECT
                client_services.client_name,
                client_services.point_id,
                client_services.u_login,
                client_services.pcross,
                service_type.servicetype_id,
                client_services.tablename AS tablelink,
                client_services.admin_id AS admin_id,
                ats_list.name AS ats,
                client_services.dslam_ip,
                client_services.portnumber,
                ports.frame_number,
                ports.state AS port_state,
                service_type.servicetype_name,
                service_type.need_cross,
                client_services.service_id,
                client_services.ip_address,
                  (select name from dslam_list where ip_address = client_services.dslam_ip) as dslam_name,
                (select typename
				 from asrtypes
				 where
					typename_value = '5'
				 and
					typename_id = (select type_id from dslam_list where ip_address = client_services.dslam_ip) ) as dslamtype1,
		 		(select speed from tarifs where tarif_id = client_services.tarif_id) as tarif_speed,
                users.first_name || ' ' || users.last_name AS admin
            FROM
               client_services
            LEFT JOIN
                users ON users.id = client_services.admin_id
            JOIN
                ats_list ON ats_list.id = client_services.ats_id
            LEFT JOIN
            	ports ON ports.id = client_services.port_id
            JOIN
                service_type ON service_type.servicetype_id = client_services.servicetype_id
		    where
				 (client_services.ip_address is null or client_services.vlan is null)
		    and
				client_services.need_cross = 1
		    and
				client_services.servicetype_id <> 3000
		    and
				client_services.admin_id = 0
		";

		return $this->_db->fetchAll($sql);
	}

	public function duplicateIPaddress($ip)
	{
		$sql = "
			select * from point_ip_addresses  where ip_address = '{$ip}'
			and
				now() between start_date and end_date
		";
		$result = $this->_db->fetchAll($sql);
		if(count($result) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}