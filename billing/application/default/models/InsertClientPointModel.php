<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Zend/Db/Table.php');

class InsertClientPointModel extends Zend_Db_Table
{
    protected $_name = 'points';
    protected $_sequence = 'points_seq';


    public function newPoint($data, $clientID, $u_login, $pwd)
    {
        $sql = "
            insert into points
                (client_id,
                 phone,
                 pcross,
				 pcross_owner,
                 connect_address,
                 country_id,
                 contact_name,
				 u_login,
				 u_passwd,
				 pwd_on_contract,
				 sign_name,
				 post_sign_name
                )
            values (
            '{$clientID}',
            '{$data['phone']}',
            '{$data['pcross']}',
			'{$data['pcross_owner']}',
            '{$data['connect_address']}',
            '{$data['country_id']}',
            '{$data['contact_name']}',
			'{$u_login}',
			'{$pwd}',
			'{$pwd}',
			'{$data['sign_name']}',
			'{$data['post_sign_name']}'
            )
        ";
		$this->_db->fetchRow($sql);
        return $this->_db->lastInsertId('points');
    }
}

?>
