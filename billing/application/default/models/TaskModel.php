<?php
/**
 * Tasks
 *
 * @author tsalik
 */

require_once 'BaseModel.php';
require_once 'WorkflowModel.php';
require_once 'TaskDocModel.php';
require_once 'DocumentReferenceModel.php';

class Task extends BaseModel
{
    protected $_name = 'tasks';
    protected $_sequence = 'tasks_seq';

    public function createTask($owner, $type, $title, $body, $deadline, $priority, $users, $docs, $allowReassignment='true')
    {
        $reference = new DocumentReference();
        $status = $reference->getOptionByNameAndGroup('new', DocumentReference::TASK_STATUSES);

        $this->startTransaction();
        try
        {
            $taskId = $this->insert(array(
                'title' => $title,
                'body' => $body,
                'deadline' => $deadline,
                'type_id' => $type,
                'priority_id' => $priority,
                'status_id' => $status['id']
            ));

            $taskDoc = new TaskDoc();
            foreach ($docs as $doc)
            {
                if (!$doc)
                    continue;

                $taskDoc->insert(array(
                    'task_id' => $taskId,
                    'document_id' => $doc
                ));
            }

            $flow = new Workflow();
            foreach ($users as $user)
            {
                if (!$user)
                    continue;

                $flow->insert(array(
                    'task_id' => $taskId,
                    'level' => 0,
                    'assigner_id' => $owner,
                    'assignee_id' => $user,
                    'status_id' => $status['id'],
                    'allow_reassignment' => $allowReassignment
                ));
            }
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            throw $ex;
        }
        $this->commitTransaction();
    }

    public function checkRights($user, $task)
    {
        $sql = "
            SELECT EXISTS (
                SELECT *
                FROM
                    tasks
                INNER JOIN workflows
                    ON tasks.id = workflows.task_id
                WHERE
                    tasks.id = {$task}
                    AND (workflows.assigner_id = {$user} OR workflows.assignee_id = {$user})) AS existence
        ";
        return $this->_db->fetchOne($sql);
    }

    public function getFullInfo($id)
    {
        $taskSql = "
            SELECT 
                t.id, t.title, t.body, t.create_date, t.deadline,
                (SELECT description FROM documents_references dr WHERE dr.id = t.type_id) AS type,
                (SELECT description FROM documents_references dr WHERE dr.id = t.priority_id) AS priority,
                (SELECT description FROM documents_references dr WHERE dr.id = t.status_id) AS status
            FROM
                tasks t
            WHERE
                t.id = {$id}
        ";
        $docsSql = "
            SELECT d.id, d.name, d.comment, d.hash, d.uplaod_date, d.mime_type
            FROM
                documents d
                INNER JOIN tasks_docs td
                    ON td.document_id = d.id
            WHERE
                td.task_id = {$id}
        ";

        return array(
            'task' => $this->_db->fetchRow($taskSql),
            'docs' => $this->_db->fetchAll($docsSql));
    }

    public function checkTaskStarted($task)
    {
        $reference = new DocumentReference();
        $status = $reference->getOptionByNameAndGroup('new', DocumentReference::TASK_STATUSES);
        $sql = "
            SELECT EXISTS(SELECT id FROM workflows WHERE status_id <> {$status['id']}) AS started
        ";
        return $this->_db->fetchOne($sql);
    }

    public function checkTaskFinished($task)
    {
        $reference = new DocumentReference();
        $status = $reference->getOptionByNameAndGroup('finished', DocumentReference::TASK_STATUSES);
        $sql = "
            SELECT NOT EXISTS(SELECT id FROM workflows WHERE status_id <> {$status['id']}) AS finished
        ";
        return $this->_db->fetchOne($sql);
    }

    public function updateStatus($task, $user, $level, $status, $message=null)
    {
        $reference = new DocumentReference();

        $this->startTransaction();
        try
        {
            $workflow = new Workflow();
            $workflow->update(array('status_id' => $status, 'message' => $message), 
                    "task_id={$task} AND \"level\"={$level} AND (assignee_id={$user} OR assigner_id={$user})");

            if ($this->checkTaskFinished($task))
            {
                $statusName = 'finished';
            }
            else if ($this->checkTaskStarted($task))
            {
                $statusName = 'started';
            }
            $status = $reference->getOptionByNameAndGroup($statusName, DocumentReference::TASK_STATUSES);
            $this->update(array('status_id' => $status['id']), "id={$task}");
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            throw $ex;
        }
        $this->commitTransaction();
    }

    public function reassign($task, $owner, $level, $assignees)
    {
        $reference = new DocumentReference();
        $status = $reference->getOptionByNameAndGroup('new', DocumentReference::TASK_STATUSES);
        $this->startTransaction();
        try
        {
            $flow = new Workflow();
            foreach ($assignees as $assignee)
            {
                $flow->insert(array(
                    'task_id' => $task,
                    'level' => 0,
                    'assigner_id' => $owner,
                    'assignee_id' => $assignee,
                    'status_id' => $status['id']
                ));
            }
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            throw $ex;
        }
        $this->rollbackTransaction();
    }

    public function getMaxUserLevel($taskId, $user)
    {
        $sql = "
            SELECT
                MAX(wf.\"level\")
            FROM
                workflows wf
            WHERE
                wf.task_id = {$taskId}
                AND wf.assigner_id = {$user}
        ";
        $level = $this->_db->fetchOne($sql);

        if ($level === null)
        {
            $sql = "
            SELECT
                MAX(wf.\"level\")
            FROM
                workflows wf
            WHERE
                wf.task_id = {$taskId}
                AND wf.assignee_id = {$user}
            ";
            $level = $this->_db->fetchOne($sql);
        }

        return $level; 
    }
}