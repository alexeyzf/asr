<?php

/**
 * Description of Select all tarifs
 *
 * @author alexuz
 * @param none
 * @param _name table
 */

require_once('Zend/Db/Table.php');

class AllService extends Zend_Db_Table
{
    protected $_name = 'tarifs';
    protected $_sequence = 'test_tarifs_seq';

}