<?php
class ModemModel extends Zend_Db_Table
{
    protected $_name = 'modems';

    public function getByID($modemID)
    {
    	if ( ! $modemID ) {
    		return array();
    	}

    	$row = $this->fetchRow("modem_id = {$modemID}");

    	if ( ! $row ) {
    		return array();
    	}

    	return $row->toArray();
    }

    //protected $_sequence = 'invoices_invoice_id_seq';

	public function getModemForClient($client_id, $point_id = null)
	{

		$sql = "
		select
			modems.*,
			(select typename from asrtypes where typename_value = '20' and typename_id = modems.modem_type )
				as modem_name
		from modems
		WHERE
			modems.client_id = {$client_id}
		";

		if($point_id)
		{
			$sql .= "
				and modems.point_id = {$point_id}
			";
		}

		return $this->_db->fetchAll($sql);
	}


    public function getModemStream($year, $clientTypeID = 1)
    {
        $startYear = $year. "-01-01";
        $endYear   = $year. "-12-31";

        $sql = "
            SELECT
				CLA.client_name,
				CLA.client_dateagree,
				M.*
            FROM
            	clients as CLA,
            	modems as M
            WHERE
				CLA.client_id = M.client_id
            	AND CLA.client_type_id = {$clientTypeID}
            	AND CLA.client_dateagree between '{$startYear}' and '{$endYear}'
            ORDER BY
            	CLA.client_dateagree
        ";

        return $this->_db->fetchAll($sql);
    }

    public function addNewModem($data)
    {
    	$sql = "
			insert into modems (modem_serial, modem_price, modem_type, service_id, client_id, table_name, point_id)
			values
			(
				'{$data['modem_serial_var']}',
				{$data['modem_price_var']},
				{$data['modem_type_var']},
				{$data['sid_var']},
				{$data['client_id_var']},
				'{$data['tablelink_var']}',
				{$data['point_id_var']}
			)
		";
		return $this->_db->fetchAll($sql);
    }

    public function updateModem($serial, $price, $modemID)
    {
    	$sql = "
			update modems set modem_serial = '{$serial}', modem_price = {$price}
			where
				modem_id = {$modemID}
		";
		return $this->_db->fetchAll($sql);
    }
}
?>