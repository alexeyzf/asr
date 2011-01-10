<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('Zend/Db/Table.php');

class ClientContract extends Zend_Db_Table
{
    protected $_name = 'contracts';
    protected $_sequence = 'contracts_seq';

    public function getContractsByClientID($clientID)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return array();
        }

        return $this->fetchAll("client_id = {$clientID}")->toArray();
    }
    
    public function getContractsOptions($clientID)
    {
    	$contracts = $this->getContractsByClientID($clientID);
    	$options = array();
    	
    	foreach ($contracts as $contract)
    	{
    		if ($contract['contract_type_id'] == 1)
    		{
    			$options[$contract['id']] = 'Договор';
    		}
    		elseif ($contract['contract_type_id'] == 2)
    		{
    			$options[$contract['id']] = 'Доп соглашение ' . $contract['contract_number'];
    		}
    	}
    	
    	return $options;
    }

	public function getLastContractID($clientID)
	{
		$sql = "
			SELECT
				contract_id
			FROM
				contracts
			WHERE
				client_id = {$clientID}
			ORDER BY
				dateagree DESC
		";

		return $this->_db->fetchOne($sql);
	}
	
	public function getLastContract($clientID)
	{
		if ( ! $clientID )
		{
			return array();
		}
		
		$sql = "
			SELECT
				*
			FROM
				contracts
			WHERE
				client_id = {$clientID}
			ORDER BY
				dateagree DESC
		";

		return $this->_db->fetchRow($sql);
	}

	public function getLastDopAgrees($client_id)
	{
		$sql  = "
		select max(contract_number) from contracts where contract_type_id = 2
		and
			client_id  = {$client_id}
		";
		return $this->_db->fetchOne($sql);
	}
	
	public function getContractByService($serviceTable, $serviceID)
	{
		$sql = "
			SELECT
				*
			FROM
				contracts
			WHERE
				service_table = '{$serviceTable}'
				AND service_id = {$serviceID}
		";
		
		return $this->_db->fetchRow($sql);
	}
	
	public function getContractByDate($clientID, $date)
	{
		$sql = "
			SELECT
				*
			FROM
				contracts
			WHERE
				client_id = {$clientID}
				AND dateagree <= '{$date}'
			ORDER BY
				dateagree DESC
		";
		
		return $this->_db->fetchRow($sql);
	}
	
	public function getContractByID($contractID)
	{
		$row = $this->fetchRow("contract_id = {$contractID}");
		
		if ($row)
		{
			return $row->toArray();
		}
		else
		{
			return array();
		}
	}
	
	public function addAgree($clientID, $dateAgree, $authID, $serviceTable = 'adsl', $serviceID = null)
	{
		$data['client_id'] = $clientID;
		$data['dateagree'] = date('Y-m-d', strtotime($dateAgree));
		$data['contract_type_id'] = 2;
 		$data['service_table'] = $serviceTable;
 		
 		if ($serviceID)
 		{
 			$data['service_id'] = $serviceID;
 		}
 		
		$data['manager_id'] = $authID;
		$this->insert($data);
	}
}