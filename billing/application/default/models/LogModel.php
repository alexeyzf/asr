<?php
require_once('Zend/Db/Table.php');

class LogModel extends Zend_Db_Table
{
    protected $_name = 'cabinet_logs';
    protected $_sequence = 'cabinet_logs_seq';

    public function getLogsByUsername($username)
    {

       if($username)
       {
          $sql = "
             select * from cabinet_logs where username = '{$username}' order by currenttime desc
          ";
              
          return $this->_db->fetchAll($sql);
       }
    }
}

?>
