<?php
/**
 * Model of admin modules table
 *
 * @author marat
 */

require_once('BaseModel.php');

class AdminModule extends BaseModel
{
    protected $_name = 'modules';
    protected $_sequence = 'modules_seq';

    public function getLabelByName($name)
    {
        $sql = "
            SELECT
                label
            FROM
                modules
            WHERE
                name = '{$name}'
        ";

        return $this->_db->fetchOne($sql);
    }
}