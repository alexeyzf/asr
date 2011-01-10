<?php
/**
 * Model for admin groups table
 *
 * @author marat
 */

require_once('BaseModel.php');

class AdminGroup extends BaseModel
{
    protected $_name = 'groups';
    protected $_sequence = 'groups_seq';

    public function getIdAndName()
    {
        $sql = "
            SELECT
                id,
                name
            FROM
                groups
        ";

        return $this->_db->fetchAll($sql);
    }
}