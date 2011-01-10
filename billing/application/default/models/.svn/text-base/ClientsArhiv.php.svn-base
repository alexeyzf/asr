<?
/**
 * Model for clients_arhiv table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class ClientsArhiv extends Zend_Db_Table
{
	protected $_name = 'clients_arhiv';

	public function getInfo($clientID)
	{
		$sql ="
            SELECT
            	CLA.*,
	            (SELECT
	            	typename
	            FROM
	            	asrtypes
	            WHERE
	            	typename_value = '7'
	            	AND typename_id = CLA.client_type_id
	            ) AS client_type,
	            -- Тип клиента

            	(SELECT
            		typename
            	FROM
            		asrtypes
            	WHERE
            		typename_value = '0'
            		AND typename_id = CLA.bank_id
            	) AS bank_name,
            	-- Наим. банка

            	(SELECT
            		typename
            	FROM
            		asrtypes
            	WHERE
            		typename_value = '2'
            		AND typename_id = CLA.ruvd_id
            	) AS ruvd_name
            	-- Наим. РУВД
            FROM
            	clients_arhiv as CLA
            WHERE
                CLA.client_id = {$clientID}
        ";

        return $this->_db->fetchRow($sql);
	}

	public function getArhivServiceByClientID($clientID)
	{
		$sql = "
			select
				CSA.*,
				(select tarif_name from tarifs where tarif_id = CSA.tarif_id) as tarif_name,
				(select ip_address from point_ip_addresses  where point_id = CSA.point_id order by end_date desc limit 1) as ip_address
			from client_services_arhiv as CSA
			where
				CSA.client_id = {$clientID}
		";
		return $this->_db->fetchAll($sql);
	}

	public function getPointsByClientID($clientID)
	{
		$sql = "
			select point_id from points where client_id = {$clientID}
		";
		return $this->_db->fetchAll($sql);
	}
}