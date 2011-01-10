<?php
require_once('BaseModel.php');

class AccidentModel extends BaseModel
{
    protected $_name = 'accident';
    protected $_sequence = 'accident_seq';

    public function getAllAccidents($flag)
    {
    	// $flag = 1 -> opened accidents

        $sql = "
            select
                    AC.*,
                    AC.id as accident_id,

                    DL.*,
                    AT.*,
                    AT.name as ats_name,
                    DL.name as dslam_name,
                    (select first_name || ' ' || last_name from users where id = AC.userid) as username,
                    (select first_name || ' ' || last_name from users where id = AC.closed_user) as closed_user_name
            from accident as AC, dslam_list as DL, ats_list as AT
            where
                    AC.dslam_id = DL.id
            and
                    AC.ats_id = AT.id
        ";

        if($flag == 1)
        {
            $sql .= "
                AND
                AC.enddate is null
                order by AC.ats_id
            ";
        }
        else
        {
            $sql .= " order by AC.startdate desc";
        }
        return $this->_db->fetchAll($sql);
    }

    public function addAccidentsDslam($dataArr)
    {
        // ID юзера который делает что то
        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();

        foreach($dataArr as $value)
        {
            $value['uid'] = $user->id;
            
            $sql = "
                select * from add_accident({$value['uid']}, '2010-04-01', '{$value['notes']}', {$value['dslam']}, {$value['ats_id']})
            ";

            $result = $this->_db->fetchAll($sql);

        }
        return $result;
    }

    public function setClosed($id)
    {
        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();
        
        $sql = "
            update accident set enddate = now(), closed_user = {$user->id} where id = {$id}
        ";
        $this->_db->fetchAll($sql);
    }
}
?>