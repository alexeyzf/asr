<?php
/**
 * Model of client_history table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class TechHistory extends Zend_Db_Table
{
    protected $_name = 'tech_history';
    protected $_sequence = 'tech_history_seq';

    public function getHistory($where = '')
    {
        $sql = "
            SELECT
                tech_history.*,
                users.first_name || ' ' || users.last_name AS user_full_name,
                ats_list.name AS ats,
                dslam_list.name AS dslam,
                point_statuses_view.label AS status_label
            FROM
                tech_history
            LEFT JOIN
                users ON users.id = tech_history.user_id
            LEFT JOIN
                ats_list ON ats_list.id = tech_history.ats_id
            LEFT JOIN
                dslam_list ON dslam_list.id = tech_history.dslam_id
            LEFT JOIN
                point_statuses_view ON point_statuses_view.code = tech_history.status
        ";

        if ($where)
        {
            $sql .= "WHERE {$where}";
        }

        $sql .= ' ORDER BY "date" DESC';

        return $this->_db->fetchAll($sql);
    }

    public function insertManualHistory($data)
    {

    	if($data['port_number'] == "")
    	{
    		$data['port_number'] = 0;
    	}

    	if($data['dslam_id'] == "")
    	{
    		$data['dslam_id'] = 0;
    	}

    	if($data['ats_id'] == "")
    	{
    		$data['ats_id'] = 0;
    	}

		$sql = "
			insert into tech_history (date, client_name, port_number, pair_number, phone,
										user_id, ats_id, dslam_id, status, client_login, frame_number, reason, action)
			VALUES
			(
				'{$data['data_add']}',
				'{$data['client_name']}',
				 {$data['port_number']},
				'{$data['pair_number']}',
				'{$data['phone']}',
				 {$data['userid']},
 				 {$data['ats_id']},
				 {$data['dslam_id']},
				 {$data['status']},
				 '{$data['client_login']}',
				 '{$data['frame_number']}',
				 '{$data['reason_add']}',
				 '{$data['action_add']}'
			)
		";

		$this->_db->fetchAll($sql);
    }

    public function getHistoryByPhone($number)
    {
        return $this->getHistory("tech_history.phone LIKE '%{$number}%'");
    }

    public function getHistoryByPhoneLastState($number)
    {
    	$date = $this->getMaxDateState($number);
        return $this->getHistory("tech_history.phone LIKE '%{$number}%' and date = '{$date}'");
    }

    public function getHistoryByEquipment($atsID, $dslamID, $portNumber)
    {
        return $this->getHistory("tech_history.ats_id = {$atsID} AND tech_history.dslam_id = {$dslamID} AND tech_history.port_number = '{$portNumber}'");
    }

    public function getHistoryByLogin($login)
    {
        return $this->getHistory("tech_history.client_login LIKE '%{$login}%'");
    }

    public function getMaxDateState($number)
    {
    	$sql = "
			select max(date) from tech_history where phone = '{$number}'
		";
		return $this->_db->fetchOne($sql);
    }


	public function verifyPcrossNumber($port_id)
	{
		$sql_ports = "
			select pcross from points where port_id = {$port_id}
		";

		$portStatus = $this->_db->fetchOne($sql_ports);

		return $portStatus;
	}

	public function getPointLogin($pointID, $flag = null)
    {
		if( $pointID != "" )
		{
			$sql = "
				(select u_login from points where point_id = {$pointID} limit 1)
				UNION ALL
				(select u_login from points_arhiv where point_id = {$pointID} limit 1)
			";

			return $this->_db->fetchOne($sql);
		}
    }
    
    public function checkPointHistory($pointID, $needStatus = -1)
    {
    	if ( ! $pointID )
    	{
    		return null;
    	}
    	
    	$sql = "
    		SELECT 
    			th.id 
    		FROM
    			points p
    		JOIN
    			clients c ON c.client_id = p.client_id
			JOIN 
				tech_history th ON th.client_login = p.u_login 
				AND th.status = {$needStatus}
				AND c.client_name = th.client_name
			WHERE
				th.date BETWEEN current_date - INTERVAL '2 months' AND current_date
				AND p.point_id = {$pointID}
			LIMIT 1
    	";
    	
    	return $this->_db->fetchOne($sql);
    }
}