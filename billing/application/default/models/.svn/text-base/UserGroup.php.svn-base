<?php
/**
 * Model of usergroup table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class UserGroup extends Zend_Db_Table
{
    protected $_name = 'usergroup';

    const STREAM_GROUP = 'stream';
    const CARD_USER_GROUP = 'CardUser';
    const ST_STREAM = 'ststream';

    public function add($userName, $groupName)
    {
        $data['username'] = $userName;
        $data['groupname'] = $groupName;

        $this->insert($data);
    }
    
    public function getGroup($userName)
    {
    	$sql = "
    		SELECT
    			groupname
    		FROM
    			usergroup
    		WHERE
    			username = '{$userName}'
    	";
    	
    	return $this->_db->fetchOne($sql);
    }
}