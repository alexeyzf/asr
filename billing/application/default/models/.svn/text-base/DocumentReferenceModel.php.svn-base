<?php
/**
 * Documents References
 *
 * @author tsalik
 */

require_once('BaseModel.php');

class DocumentReference extends BaseModel
{
    const TASK_TYPES = 'task-types';
    const TASK_PRIORITIES = 'task-priorities';
    const TASK_PROPERTIES = 'task-properties';
    const TASK_STATUSES = 'task-statuses';
    const FIELD_TYPES = 'field-types';
    const FIELD_OPTIONS = 'field-options';

    protected $_name = 'documents_references';
    protected $_sequence = 'documents_references_seq';

    public function getOptionsByGroupName($name)
    {
        $sql = "
            SELECT
                id, name, description
            FROM
                documents_references
            WHERE
                \"group\" =
                    (SELECT
                        id
                    FROM
                        documents_references
                    WHERE name='{$name}')
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getOptionByNameAndGroup($name, $group)
    {
        $sql = "
            SELECT
                id, name, description
            FROM
                documents_references
            WHERE
                \"group\" =
                    (SELECT
                        id
                    FROM
                        documents_references
                    WHERE name='{$group}')
                AND name = '{$name}'
        ";
        return $this->_db->fetchRow($sql);
    }
}