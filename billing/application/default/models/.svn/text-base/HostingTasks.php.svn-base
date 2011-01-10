<?php
require_once('BaseModel.php');

class HostingTasks extends BaseModel
{
    protected $_name = 'hostingtasks';
    protected $_sequence = 'hostingtasks_seq';

    public function insertHostingTask($data)
    {
		$sql = "
			insert into hostingtasks (web_domain, notes, managerid, point_id, task_type)
			values
			(
				'{$data['domain_addres']}',
				'Выключение в ручную ',
				{$data['managerid']},
				{$data['point_id']},
				{$data['task_type']}
			)
		";
		$this->_db->fetchAll($sql);
    }
}