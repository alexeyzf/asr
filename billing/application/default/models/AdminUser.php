<?php
/**
 * Model for usres table
 *
 * @author marat
 */

require_once('BaseModel.php');

class AdminUser extends BaseModel
{
    protected $_name = 'users';
    protected $_sequence = 'users_seq';

    public function getUserLogin($id)
    {
    	$sql = "
            SELECT
                login
            FROM
                users
            WHERE
		users.id = {$id}
	";

	return $this->_db->fetchOne($sql);
    }

    public function getUserFullName($id)
    {
        $id = intval($id);

        if ( ! $id )
        {
            return false;
        }

        $sql = "
            SELECT
                users.first_name || ' ' || users.last_name
            FROM
                users
            WHERE
                users.id = {$id}
        ";

        return $this->_db->fetchOne($sql);
    }

    public static function groupById($users)
    {
        $group = array();
        foreach ($users as $user)
        {
            $group[$user['user_id']] = $user;
        }
        return $group;
    }
    
    public static function groupByGroup($users)
    {
        $group = array();
        foreach ($users as $user)
        {
            $group[$user['group_name']][] = $user;
        } 
        return $group;
    }

    public function getIdAndFullName()
    {
        $sql = "
            SELECT
                u.id as user_id,
                u.first_name || ' ' || u.last_name as full_name,
                g.name as group_name,
                g.id as group_id
            FROM
                users u
                INNER JOIN user_groups ug
                    ON u.id = ug.user_id
                INNER JOIN groups g
                    ON ug.group_id = g.id
            ORDER BY g.name
        ";

        return $this->_db->fetchAll($sql);
    }

    public function getUsersByGroupIds($ids)
    {
        $idsClause = implode(',', $ids);
        $sql = "
            SELECT
                u.id as user_id,
                u.first_name || ' ' || u.last_name as full_name,
                g.name as group_name
            FROM
                users u
                INNER JOIN user_groups ug
                    ON u.id = ug.user_id
                INNER JOIN groups g
                    ON ug.group_id = g.id
            WHERE
                g.id in ({$idsClause})
        ";

        return $this->_db->fetchAll($sql);
    }
}
