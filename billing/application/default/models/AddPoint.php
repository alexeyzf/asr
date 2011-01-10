<?php
/**
 * Description of Add a some points to table points
 *
 * @author alex
 * @param none
 * @param _name table
 * @param _sequence identity
 */

require_once('Zend/Db/Table.php');

class AddPoint extends Zend_Db_Table
{
    protected $_name = 'points';
    protected $_sequence = 'points_seq';

    public function getInfo($pointID, $clientID = "")
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return array();
        }

        $sql = "
            SELECT
                points.*,
                clients.client_name,
                ats_list.name AS ats,
                dslam_list.name AS dslam,
                dslam_list.ip_address AS dslam_ip,
                ports.state AS port_state,

                (select label from point_statuses_view where code = points.statuscross)
                as label_cross

            FROM
                points
            LEFT JOIN
                clients ON clients.client_id = points.client_id
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                dslam_list ON dslam_list.id = points.dslam_id
            LEFT JOIN
                ports ON ports.id = points.port_id
        ";

        if($clientID)
        {
            $sql .= " WHERE points.client_id = {$clientID}";
        }
        else
        {
            $sql .= " WHERE points.point_id = {$pointID}";
        }

        return $this->_db->fetchAll($sql);
    }


    /**
     * Get list of points
     *
     * @param string $where - Filter condition
     * @return array
     */
    public function getList($where = '', $order = '')
    {
		$sql = "
			SELECT
				points.*,
				clients.client_name,
				clients.ballance_change,
                clients.client_type_id,
				ats_list.name AS ats,
				dslam_list.name AS dslam,
				dslam_list.ip_address AS dslam_ip,
				phone_hub_list.name AS phone_hub_name
			FROM
                points
                LEFT JOIN clients
                    ON clients.client_id = points.client_id
                LEFT JOIN ats_list
                    ON ats_list.id = points.ats_id
                LEFT JOIN dslam_list
                    ON dslam_list.id = points.dslam_id
                LEFT JOIN phone_hub_list
                    ON phone_hub_list.id = ats_list.phone_hub_id
        ";

        if ($where)
        {
            $sql .= "WHERE $where";
        }

        if ($order)
        {
            $sql .= " ORDER BY {$order} ";
        }

        return $this->_db->fetchAll($sql);
    }

    public function getJuridicalList($order)
    {
        return $this->getList('clients.client_type_id = 0', $order);
    }

    public function getCrossList($where = '', $order = '')
    {
        $sql = "
            SELECT
                points.*,
                clients.client_name,
                contracts.dateagree,
                ats_list.name AS ats,
                dslam_list.name AS dslam,
                ports.number AS port_number,
                ports.frame_number AS port_frame_number,
                ports.line_number1 AS port_line_number1,
                ports.line_number2 AS port_line_number2,
                point_statuses_view.label AS point_status,
                point_statuses_view.type AS point_status_type
            FROM
                points
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                dslam_list ON dslam_list.id = points.dslam_id
            LEFT JOIN
                ports ON ports.id = points.port_id
            LEFT JOIN
                clients ON clients.client_id = points.client_id
            LEFT JOIN
                contracts AS contracts ON contracts.client_id = clients.client_id
                AND contract_type_id = 1
            LEFT JOIN
                point_statuses_view ON point_statuses_view.code = points.statuscross
        ";

        if ($where)
        {
            $sql .= " WHERE $where ";
        }

        if ($order)
        {
            $sql .= " ORDER BY {$order} ";
        }

        return $this->_db->fetchAll($sql);
    }

    public function fetchRecordByID($pointID)
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return $this->createRow();
        }

        return $this->fetchRow("point_id = {$pointID}");
    }

    public function saveChanges($data, $pointID = NULL)
    {
    	$data['last_modified_date'] = date('Y-m-d h:m:s');

        $pointID = intval($pointID);

        if ($pointID)
        {
            $this->update($data, "point_id = {$pointID}");
        }
        else
        {
            $this->insert($data);
        }
    }
    public function ballanceCarryingOver($client_id, $user_id, $login)
    {
        $sql = "
         select balance_carrying_over('{$login}', {$client_id}, {$user_id});
        ";
        return $this->_db->fetchAll($sql);
    }

    public function blockPointCorp($data, $start)
    {
    	$sql = "
			insert into blocks (point_id, block_date)
			values
			(
				{$data['point_id']},
				'{$start}'
			)
		";
		$this->_db->fetchAll($sql);
    }

    public function getBlockPointCorp($pointID)
    {
		$sql = "
			select * from blocks where point_id = {$pointID} and is_blocked = false limit 1
		";
		return $this->_db->fetchRow($sql);
    }

}
