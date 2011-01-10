<?php
/**
 * Model of ports table
 *
 * @author marat
 */

require_once('BaseModel.php');

class Ports extends BaseModel
{
    protected $_name = 'ports';
    protected $_sequence = 'ports_seq';

    public $statuses = array (
    	0		=> 'Выключен',
    	1		=> 'Включен',
    	-1		=> 'Битый порт',
    	-2		=> 'Битая пара',
    	-3		=> 'Битая рамка',
    	-4		=> 'Битая патч-панель',
    	-5		=> 'Битый',
    	-6		=> 'Телефония'
    );

    const EMPTY_STATUS = 0;

    const OCCUPED_STATUS = 1;

    const PORT_BROKEN_STATUS = -1;

    const PORT_BROKEN_STATUS_UNKNOWN = -1;

    const PAIR_BROKEN_STATUS = -2;

    const FRAME_BROKEN_STATUS = -3;

    const PATCH_BROKEN_STATUS = -4;

    const BROKEN_STATUS = -5;

    const PHONE_STATUS = -6;

    /**
     * Gets ports list info - for dslam list page
     */
    public function getPortList($dslamIDs = null, $atsID = null, $order='number')
    {
        if (is_array($dslamIDs) && count($dslamIDs) > 0)
        {
            $dslams = implode(',', $dslamIDs);
            $where = " WHERE ports.dslam_id in ({$dslams}) ";
        }

  		if ($atsID)
  		{
  			if ($where)
  			{
  				$where .= " AND dslam_list.ats_id = {$atsID} ";
  			}
  			else
  			{
  				$where = " WHERE dslam_list.ats_id = {$atsID} ";
  			}
  		}

        $sql = "
            SELECT
                ports.*,
                points.point_id,
                points.pcross,
                points.pcross_type,
                clients.client_id,
                clients.client_name,
                clients.client_type_id,
                dslam_list.ats_id,
                dslam_list.name AS dslam_name,
                dslam_list.ip_address AS dslam_ip,
                dslam_list.type_id AS dslam_type_id,
                (SELECT
                    speed
                 FROM
                    client_services
                 WHERE
                    client_services.point_id = points.point_id
                    AND speed IS NOT NULL
                 LIMIT 1
                ) AS portspeed
            FROM
                ports
            LEFT JOIN
                points ON points.port_id = ports.id
            LEFT JOIN
                clients ON clients.client_id = points.client_id
            LEFT JOIN
            	dslam_list ON dslam_list.id = ports.dslam_id
            {$where}
            ORDER BY
                {$order}
        ";

        return $this->_db->fetchAll($sql);
    }

    /**
     * Gets first avaliable recommended empty port in dslam
     *
     * @param integer $dslamID - Dslam ID
     */
    public function getFirstAvailablePort($dslamID)
    {
        if ( ! $dslamID )
        {
            $dslamID = "0";
        }

        $port = $this->fetchRow("status = " . self::EMPTY_STATUS . " AND dslam_id = {$dslamID}", 'last_using_date');

        if ( ! $port )
        {
            $port = $this->createRow();
        }

        return $port;
    }

    /**
     * Gets id=>number array for select options
     *
     * @param string $where - filter condition
     */
    public function getOptions($where)
    {
        $portsList = $this->fetchAll($where, 'number');
        $portsOptions = array();

        foreach ($portsList as $port)
        {
            $portsOptions[$port->id] = $port->number;
        }

        return $portsOptions;
    }

    /**
     * Gets number => number array for select options
     *
     * @param string $where - filter condition
     */
    public function getPortsNumbers($where)
    {
        $portsList = $this->fetchAll($where);
        $portsOptions = array();

        foreach ($portsList as $port)
        {
            $portsOptions[$port->number] = $port->number;
        }

        return $portsOptions;
    }

    public function getDslamPorts($dslamID)
    {
        $dslamID = intval($dslamID);

        if ( ! $dslamID )
        {
            return array();
        }

        return $this->fetchAll("dslam_id = {$dslamID}", "number")->toArray();
    }

    public function getDslamPortNumbers($dslamID)
    {
        $dslamID = intval($dslamID);

        if ( ! $dslamID )
        {
            return array();
        }

        return $this->getPortsNumbers("dslam_id = {$dslamID}");
    }

    public function getDslamPortsCount($dslamID)
    {
        $dslamID = intval($dslamID);

        if ( ! $dslamID )
        {
            return 0;
        }

        $sql = "
            SELECT
                COUNT(number)
            FROM
                ports
            WHERE
                dslam_id = {$dslamID}
        ";

        return $this->_db->fetchOne($sql);
    }

    public function getDslamMaxPortNumber($dslamID)
    {
        $dslamID = intval($dslamID);

        if ( ! $dslamID )
        {
            return 0;
        }

        $sql = "
            SELECT
                MAX(number)
            FROM
                ports
            WHERE
                dslam_id = {$dslamID}
        ";

        return $this->_db->fetchOne($sql);
    }

    /**
     * Return status label by status code
     *
     * @param integer $statusCode Status Code
     */
    public function getStatusLabel($statusCode)
    {
    	return $this->statuses[$statusCode];
    }

    public function changeStatus($portID, $status)
    {
    	$data['status'] = $status;
    	$this->update($data, "id = {$portID}");
    }

    public function getAtsFrames($atsID)
    {
    	return $this->_db->select()->from($this->_name, array('frame_number'))
    		->join('dslam_list', "dslam_list.id = {$this->_name}.dslam_id", array())
    		->where("dslam_list.ats_id = {$atsID}")
    		->order("{$this->_name}.frame_number")
    		->query()->fetchAll();
    }

    public function getBrokenPort()
    {
    	$sql = "
			select
				PRT.*,
				(select ip_address from dslam_list where id = PRT.dslam_id) as ip_dslam,
				(select ats_list.name from dslam_list, ats_list
						where dslam_list.ats_id = ats_list.id
						and
							  dslam_list.id = PRT.dslam_id
				) as ats_name
			from ports as PRT
			where
				status < 0
			order by PRT.dslam_id, PRT.number
		";
		return $this->_db->fetchAll($sql);
    }

    public function setStatusFromBrokenPort($id)
    {
    	$sql = "
			select * from repair_port({$id})
		";

		return $this->_db->fetchAll($sql);
    }

    public function startPerekros($data)
    {

        $sql = "
            select * from perekros({$data['point_id']}, {$data['pcross_type']}, {$data['ats_id']}, {$data['dslam_id']}, {$data['port_id']}, '{$data['pcross']}', '{$data['new_login']}')
        ";

        $result = $this->_db->fetchOne($sql);
        return $result;
    }
}