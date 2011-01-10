<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('Zend/Db/Table.php');

class InsertClientContract extends Zend_Db_Table
{
    protected $_name = 'contracts';
    protected $_sequence = 'contracts_seq';

}

?>
