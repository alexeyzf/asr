<?php
require_once('Zend/Db/Table.php');

class NotesModel extends Zend_Db_Table
{
    protected $_name = 'office_notes';
    protected $_sequence = 'office_notes_seq';

    public function getListNotes($point_id)
    {


        $sql = "
            select
                OFN.*,
                (select (first_name || ' ' || last_name)  from users where id = OFN.userid) as userface
            from office_notes as OFN
            where
                point_id = {$point_id}
            order by OFN.id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function addNotes($data)
    {
        if($data['main_note'] == "")
        {
            return;
        }
        
        $sql = "
            insert into office_notes (textnote, point_id, userid)
            values
            (
                '{$data['main_note']}',
                {$data['pid']},
                {$data['uid']}
            )
        ";

        $this->_db->fetchAll($sql);
    }
}