<?php
/**
 * Description of Add a New Clients
 *
 * @author alexuz
 * @param none
 * @param _name table
 * @param _sequence identity
 */

require_once('Zend/Db/Table.php');

class AddClient extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    public function updateClientInfo($clientinfo,$cl_uid, $point_id, $client_type_id)
    {

    	 if($client_type_id == "1")
    	 {
			$sqlClients = "
		        update clients set
		            client_name   = '{$clientinfo['client_name']}',
		            address       = '{$clientinfo['address']}',
		            phone         = '{$clientinfo['phone']}',
		            email         = '{$clientinfo['email']}',
		            passp_n       = '{$clientinfo['passp_n']}',
		            client_orient = '{$clientinfo['client_orient']}',
		            fax           = '{$clientinfo['fax']}',
					ruvd_id       = {$clientinfo['ruvd_id']}
				where
		        client_id = {$cl_uid}
        		";
    	 }
    	 else
    	 {
    	 	$sqlClients = "
		        update clients set
		            client_name   = '{$clientinfo['client_name']}',
		            address       = '{$clientinfo['address']}',
		            phone         = '{$clientinfo['phone']}',
		            email         = '{$clientinfo['email']}',
		            passp_n       = '{$clientinfo['passp_n']}',
		            client_orient = '{$clientinfo['client_orient']}',
		            fax           = '{$clientinfo['fax']}',
					inn			  = '{$clientinfo['inn']}',
					mfo			  = '{$clientinfo['mfo']}',
					okonx		  = '{$clientinfo['okonx']}',
					bank_id		  = '{$clientinfo['bank_id']}'
				where
		        client_id = {$cl_uid}
        		";
    	 }


        $sqlPoints = "
        update points set country_id = {$clientinfo['country_id']}
        where
            point_id = {$point_id}
            and
            client_id = {$cl_uid}
        ";
        $this->_db->fetchAll($sqlClients);
        return $this->_db->fetchAll($sqlPoints);
    }
}

?>
