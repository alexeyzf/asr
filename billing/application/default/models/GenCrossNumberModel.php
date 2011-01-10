<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Zend/Db/Table.php');

class GenCrossNumberModel extends Zend_Db_Table
{
    protected $_name = 'asrtypes';
    protected $_sequence = 'asrtypes_seq';

    public function selectCountryPrefix($country)
    {
        $sql = "
            select typename_desc from asrtypes
            where
                typename_value = '1'
            and
                typename_id = '{$country}';
        ";
        return $this->_db->fetchOne($sql);
    }
}
?>
