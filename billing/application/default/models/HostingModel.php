<?php
require_once('BaseModel.php');

class HostingModel extends BaseModel
{
    protected $_name = 'hosting';
    protected $_sequence = 'hosting_seq';

    public function getByClientID($clientID)
    {
    	$sql = "
    		SELECT
				hosting.*
			FROM
				hosting
			JOIN
				points on points.point_id = hosting.point_id
			JOIN
				clients on clients.client_id = points.client_id
			WHERE
				points.client_id = {$clientID}
				AND hosting.startdate <= current_date
				AND hosting.enddate > current_date
				AND (clients.client_type_id != 0 OR hosting.is_forced = true)
    	";

    	return $this->_db->fetchAll($sql);
    }
}