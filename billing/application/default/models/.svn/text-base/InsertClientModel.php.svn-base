<?php
/**
 * Model of clients table
 */

require_once('Zend/Db/Table.php');
require_once('TarifListModel.php');
require_once('AdminUser.php');

class InsertClientModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    const CLIENT_TYPE_JURIDICAL = 0;
    const CLIENT_TYPE_PHYSICAL = 1;

    /**
     * Searches and return list of clients where $param mathing $value
     *
     * @param string $param - criteria to search
     * @param string $value - values to match
     */
    public function search($param, $value)
    {
        $sql ="
            SELECT
                points.point_id,
                clients.client_id,
                clients.client_name,
                ats_list.name AS ats,
                points.u_login,
                points.phone AS contact_phone,
                points.pcross AS cross_phone,
                points.connect_address,
                ports.state
            FROM
                clients
            LEFT JOIN
                points ON points.client_id = clients.client_id
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                ports ON ports.id = points.port_id
            WHERE
                LOWER({$param}) like LOWER('%{$value}%')
        ";

        return $this->_db->fetchAll($sql);
    }

    /**
     * Gets cleint info - used in techcleint pages
     *
     * @param integer $pointID - Point ID to show infromation for
     */
    public function getInfo($pointID)
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return array();
        }

        $sql = "
            SELECT
                clients.client_id,
                clients.client_name,
                clients.client_type_id,
                clients.ballance,
                client_type.typename,
                points.point_id,
                points.connect_address,
                contracts.dateagree,
                contracts.manager_id,
                points.crossdate,
                points.u_login,
                points.country_id,
                points.pcross,
                points.pcross_type,
                points.pcross_owner,
                points.ats_id,
                points.dslam_id,
                points.port_id,
                points.statuscross,
                point_statuses_view.label AS statuscross_label,
                points.engineer_id,
                ports.number AS port_number,
                ports.frame_number AS frame_number,
                ports.line_number1,
                ports.line_number2,
                ports.status,
                ports.state AS port_state,
                dslam_list.ip_address AS dslam_ip,
                dslam_list.name AS dslam_name,
                ats_list.name AS ats_name
            FROM
                clients AS clients
            LEFT JOIN
                points AS points ON points.client_id = clients.client_id
            LEFT JOIN
                asrtypes AS client_type ON client_type.typename_id = clients.client_type_id
                    AND client_type.typename_value = '7'
            LEFT JOIN
                point_statuses_view ON point_statuses_view.code = points.statuscross
            LEFT JOIN
                contracts AS contracts ON contracts.client_id = clients.client_id
            LEFT JOIN
                ports ON ports.id = points.port_id
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                dslam_list ON dslam_list.id = points.dslam_id
            WHERE
                points.point_id = {$pointID}
        ";

        $info = $this->_db->fetchRow($sql);
        $info['dateagree'] = date('d.m.y', strtotime($info['dateagree']));

        if ($info['crossdate'])
        {
            $info['crossdate'] = date('d.m.y', strtotime($info['crossdate']));
        }
        else
        {
            $info['crossdate'] = 'Не скроссирован';
        }

        $tarifListModel = new TarifListModel();
        $info['portspeed'] = $tarifListModel->getSpeed($pointID);

        $adminUser = new AdminUser();
        $info['manager_name'] = $adminUser->getUserFullName($info['manager_id']);
        $info['engineer_name'] = $adminUser->getUserFullName($info['engineer_id']);
        return $info;
    }


    public function addRschet($clientID,$schet)
    {
        /**
        *  Метод записывает все расчетные счита клиентов в таблицу rschet
        */
        $sql = "insert into rschet (client_id, schet)
                values ('{$clientID}', '{$schet}')
        ";
        return $this->_db->fetchRow($sql);
    }

    public function varifyRschetForAddClient($arrRschet)
    {
		$result = 0;

		foreach($arrRschet as $value)
		{
			$sql = "
				select 
					rschet_id 
				from 
					rschet  
				where 
					schet = '{$value}'
					AND client_id NOT IN (select client_id from clients_arhiv)";
			$id = $this->_db->fetchOne($sql);
			if($id)
			{
				$result = 1;
			}
		}

        if($result == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function markAsEmployee($clientID)
    {
        if ( ! $clientID )
        {
            return;
        }

        $data['is_employee'] = new Zend_Db_Expr('true');;
        $data['is_donate'] = new Zend_Db_Expr('false');

        $this->update($data, "client_id = {$clientID}");
    }

    public function markAsDonate($clientID)
    {
        if ( ! $clientID )
        {
            return;
        }

        $data['is_employee'] = new Zend_Db_Expr('false');
        $data['is_donate'] = new Zend_Db_Expr('true');

        $this->update($data, "client_id = {$clientID}");
    }
}

?>
