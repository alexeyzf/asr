<?php
/**
 * Model of radcheck table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class Radcheck extends Zend_Db_Table
{
    protected $_name = 'radcheck';
    protected $_sequence = 'radcheck_id_seq';
	
    public function deleteUser($userName, $serviceType)
    {
    	$this->delete("username = '{$userName}' AND servicetype = {$serviceType}");
    }
    
    public function addUser($userName, $password, $serviceType)
    {
        $data['username'] = $userName;
        $data['attribute'] = 'Password';
        $data['op'] = '==';
        $data['servicetype'] = $serviceType;
        $data['value'] = $password;

        return $this->insert($data);
    }
    
    public function checkPassword($login, $password)
    {
    	$radcheckData = $this->fetchRow("username = '{$login}' AND attribute like '%Passw%'");
    	
    	if ($radcheckData)
    	{
    		if ($radcheckData->attribute == 'Password')
    		{
    			return $password == $radcheckData->value;
    		}
    		elseif ($radcheckData->attribute == 'Crypt-Password')
    		{
    			return crypt($password, 'PL') == $radcheckData->value;
    		}    		
    	}
    	
    	return false;
    }
}