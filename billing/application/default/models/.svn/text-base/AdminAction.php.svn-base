<?php
/**
 * Model of admin actions table
 *
 * @author marat
 */

require_once('BaseModel.php');

class AdminAction extends BaseModel
{
    protected $_name = 'actions';
    protected $_sequence = 'actions_seq';

    public function getLabelByName($controllerName, $name)
    {
        $sql = "
            SELECT
                label
            FROM
                actions
            WHERE
                name = '{$name}'
                AND (SELECT modules.name FROM modules WHERE modules.id = actions.module_id) = '{$controllerName}'
        ";

        return $this->_db->fetchOne($sql);
    }
}