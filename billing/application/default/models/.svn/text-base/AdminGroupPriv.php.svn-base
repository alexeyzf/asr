<?php
/**
 * Model for admin group_privileges
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class AdminGroupPriv extends Zend_Db_Table
{
    protected $_name = 'group_privileges';
    protected $_sequence = 'group_privileges_seq';

    /**
     * Gets list of modules and acctions on which user has privileges
     *
     * @param int $userID _ User ID
     * @return array - List of modules and actions
     */
    public function getPrivilegesByUserID($userID)
    {
        $sql = "
            SELECT
                modules.name AS module_name,
                actions.name AS action_name
            FROM
                group_privileges
                LEFT JOIN user_groups ON user_groups.group_id = group_privileges.group_id
                LEFT JOIN modules ON modules.id = group_privileges.module_id
                LEFT JOIN actions ON actions.id = group_privileges.action_id
           WHERE
                user_groups.user_id = '{$userID}'
                AND modules.is_deleted = false
                AND actions.is_deleted = false
        ";

         $result = $this->_db->fetchAll($sql);

         return $result;
    }

    public function fetchGroupPrivs($groupID)
    {
        $groupID = intval($groupID);

        if ( ! $groupID )
        {
            return array();
        }

        return $this->fetchAll("group_id = {$groupID}");
    }

    /**
     * Deletes group privileges by group ID
     *
     * @param int $groupID  - Group ID
     */
    public function deleteGroupPrivs($groupID)
    {
        $groupID = intval($groupID);

        if ( ! $groupID )
        {
            return;
        }

        $this->delete("group_id = {$groupID}");
    }
}