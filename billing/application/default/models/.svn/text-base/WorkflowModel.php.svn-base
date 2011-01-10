<?php
/**
 * Workflows
 *
 * @author tsalik
 */

require_once('BaseModel.php');

class Workflow extends BaseModel
{
    protected $_name = 'workflows';
    protected $_sequence = 'workflows_seq';

    /*
     * Группирует назначения заданий по Id задания и по уровню переназначений.
     */
    public static function groupWorkflows($workflows)
    {
        $groups = array();
        foreach ($workflows as $row)
        {
            $groupId = $row['id'];
            $level = $row['level'];
            $groups[$groupId][$level][] = $row;
        }
        return $groups;
    }

    public function getTasksAssingedOnUser($user, $active=true)
    {
        $sql = "
            SELECT
                t.id, \"level\", t.title, t.body, t.create_date, t.deadline,
                (SELECT description FROM documents_references dr WHERE dr.id = t.type_id) AS type,
                (SELECT description FROM documents_references dr WHERE dr.id = t.priority_id) AS priority,
                (SELECT description FROM documents_references dr WHERE dr.id = t.status_id) AS status,
                (SELECT u.first_name || ' ' || u.last_name FROM users u WHERE u.id = wf.assigner_id) AS assigner,
                (SELECT u.first_name || ' ' || u.last_name FROM users u WHERE u.id = wf.assignee_id) AS assignee
            FROM
                tasks t
                INNER JOIN workflows wf
                    ON t.id = wf.task_id
            WHERE
                wf.assignee_id = {$user} ";
        if ($active)
        {
            $sql .= "AND t.status_id <> (SELECT id FROM documents_references WHERE name='finished') ";
        }
        $sql .= "
        ORDER BY
            t.create_date, t.status_id";

        return $this->_db->fetchAll($sql);
    }

    public function getTasksCreateByUser($user, $reassigned=false, $active=true)
    {
        $sql = "
            SELECT
                t.id, \"level\", t.title, t.body, t.create_date, t.deadline,
                (SELECT description FROM documents_references dr WHERE dr.id = t.type_id) AS type,
                (SELECT description FROM documents_references dr WHERE dr.id = t.priority_id) AS priority,
                (SELECT description FROM documents_references dr WHERE dr.id = t.status_id) AS status,
                (SELECT u.first_name || ' ' || u.last_name FROM users u WHERE u.id = wf.assigner_id) AS assigner,
                (SELECT u.first_name || ' ' || u.last_name FROM users u WHERE u.id = wf.assignee_id) AS assignee
            FROM
                tasks t
                INNER JOIN workflows wf
                    ON t.id = wf.task_id
            WHERE
                wf.assigner_id = {$user} ";
        if ($active)
        {
            $sql .= "
                AND t.status_id <> (SELECT id FROM documents_references WHERE name='finished') ";
        }
        if ($reassigned)
        {
            $sql .= "
                AND \"level\" > 1";
        }

        $sql .= "
        ORDER BY
            t.create_date, t.status_id";

        return $this->_db->fetchAll($sql);
    }

    public function getRow($user, $task, $level)
    {
        $sql = "
            SELECT
                id,
                task_id,
                \"level\",
                assigner_id,
                assignee_id,
                status_id,
                message
            FROM
                workflows
            WHERE
                task_id = {$task}
                AND \"level\" = {$level}
                AND (assigner_id = {$user} OR assignee_id = {$user})
        ";

        return $this->_db->fetchRow($sql);
    }
}