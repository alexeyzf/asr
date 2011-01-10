<?php
/**
 * Model fro user_groups table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class AdminUserGroup extends Zend_Db_Table
{
    protected $_name = 'user_groups';
    protected $_sequence = 'user_groups_seq';

    public function fetchUserGroups($userID)
    {
        $userID = intval($userID);

        if ( ! $userID )
        {
            return array();
        }

        return $this->fetchAll("user_id = {$userID}");
    }

    public function deleteUserGroups($userID)
    {
        $userID = intval($userID);

        if ( ! $userID )
        {
            return;
        }

        $this->delete("user_id = {$userID}");
    }

    public function insertUserGroups($userGroups, $userID)
    {
        foreach ($userGroups as $groupID)
        {
            $userGroupData = array();
            $userGroupData['user_id'] = $userID;
            $userGroupData['group_id'] = $groupID;
            $this->insert($userGroupData);
        }
    }
}