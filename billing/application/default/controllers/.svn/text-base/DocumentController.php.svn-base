<?php
require_once 'BaseController.php';
require_once 'models/AdminUser.php';
require_once 'models/AdminGroup.php';
require_once 'models/DocumentReferenceModel.php';
require_once 'models/InnerDocumentModel.php';
require_once 'models/InnerDocumentFileModel.php';
require_once 'models/WorkflowModel.php';
require_once 'models/TaskModel.php';
require_once 'forms/NewTaskForm.php';

class DocumentController extends BaseController
{
    /*
     * В метод передаются данные уже сгрупперованные
     * по заданию и уровню переназначения.
     * Метод же схлопывет часть лишних данных
     */
    private function groupDataForReport($data)
    {
        $result = array();
        foreach ($data as $group)
        {
            foreach ($group as $level)
            {
                $item = array(
                    'task_id' => $level[0]['id'],
                    'level' => $level[0]['level'],
                    'title' => $level[0]['title'],
                    'body' => $level[0]['body'],
                    'status' => $level[0]['status'],
                    'priority' => $level[0]['priority'],
                    'create_date' => date('Y-m-d', strtotime($level[0]['create_date'])),
                    'deadline' => date('Y-m-d', strtotime($level[0]['deadline'])),
                    'assigner' => $level[0]['assigner']
                );

                $assignees = array();
                foreach ($level as $flow)
                {
                    $assignees[] = $flow['assignee'];
                }
                $item['assignees'] = implode(', ', $assignees);

                $result[] = $item;
            }
        }
        return $result;
    }

    // List active tasks.
    public function indexAction()
    {
        $work = new Workflow();
        $user = Zend_Auth::getInstance()->getStorage()->read();

        $this->view->data = array(
            array('values' => $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksCreateByUser($user->id))),
                  'title' => 'Созданные мной задания',
                  'url' => '/document/my-tasks/by-me/true/'),
            array('values' => $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksCreateByUser($user->id, true))),
                  'title' => 'Здания на моем контроле',
                  'url' => '/document/my-tasks/by-me/true/reassigned/true/'),
            array('values' => $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksAssingedOnUser($user->id))),
                  'title' => 'Назначенные на меня задания',
                  'url' => '/document/my-tasks/on-me/true/'));
    }

    public function showEditTaskAction()
    {
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $task = new Task();
        $taskId = $this->_request->getParam('id');
        $level = $task->getMaxUserLevel($taskId, $user->id);

        $reference = new DocumentReference();
        $users = new AdminUser();
        $groups = new AdminGroup();
        $flow = new Workflow();

        $this->view->properties = array(
            'users' => AdminUser::groupByGroup($users->getIdAndFullName()),
            'groups' => $groups->getIdAndName(),
            'statuses' => $reference->getOptionsByGroupName(DocumentReference::TASK_STATUSES)
        );

        if ($task->checkRights($user->id, $taskId))
        {
            $this->view->data = array(
                'task' => $task->getFullInfo($taskId),
                'flow' => $flow->getRow($user->id, $taskId, $level));
        }
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            if ($postData['change-status'])
            {
                $task->updateStatus($taskId, $user->id, $level, $postData['status']);
            }
            else if ($postData['reassign-task'])
            {
                $task->reassign($taskId, $user->id, $level, $this->mergeUsersAndGroups($postData['assignee'], $postData['group']));
            }
            $this->_redirect("/document/show-edit-task/id/{$taskId}/");
        }
    }

    public function myTasksAction()
    {
        $work = new Workflow();
        $user = Zend_Auth::getInstance()->getStorage()->read();

        if ($this->_request->getParam('by-me'))
        {
            if ($this->_request->getParam('reassigned'))
            {
                $this->view->type = 'by-me-reassigned';
                $this->view->data = $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksCreateByUser($user->id, true, false)));
            }
            else{
                $this->view->type = 'by-me';
                $this->view->data = $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksCreateByUser($user->id, false, false)));
            }
        }
        else if ($this->_request->getParam('on-me'))
        {
            $this->view->type = 'on-me';
            $this->view->data = $this->groupDataForReport(Workflow::groupWorkflows($work->getTasksAssingedOnUser($user->id, false)));
        }
    }

    private function mergeUsersAndGroups($userIds, $groupIds)
    {
        $users = new AdminUser();
        $fiteredGroupIds = array();
        foreach ($groupIds as $id)
        {
            if ($id)
            {
                $fiteredGroupIds[] = $id;
            }
        }
        $groupUserIds = array();
        if ($fiteredGroupIds)
        {
            foreach ($users->getUsersByGroupIds($fiteredGroupIds) as $row)
            {
                $groupUserIds[] = $row['user_id'];
            }
        }

        return array_merge($userIds, $groupUserIds);
    }

    public function addTaskAction()
    {
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $reference = new DocumentReference();
        $users = new AdminUser();
        $groups = new AdminGroup();
        $docs = new InnerDocument();
        $data = array(
            'users' => AdminUser::groupByGroup($users->getIdAndFullName()),
            'groups' => $groups->getIdAndName(),
            'types'  => $reference->getOptionsByGroupName(DocumentReference::TASK_TYPES),
            'priorities' => $reference->getOptionsByGroupName(DocumentReference::TASK_PRIORITIES),
            'docs' => $docs->getIdAndNameByOwner($user->id));
        $form = new Form_NewTaskForm();
        $form->populate($data);

        $this->view->data = $data;
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if ($form->isValid($postData) && $postData['type'] && $postData['assignee'][0] /*&& $postData['document'][0]*/)
            {
                $formData = $form->getValues();
                /*var_dump(array(
                    'user' => $user->id,
                    'type' => $postData['type'],
                    'title' => $formData['title'],
                    'body' => $formData['body'],
                    'deadline' => $formData['deadline'],
                    'priority' => $formData['priority'],
                    'assignee' => $postData['assignee'],
                    'group' => $postData['group'],
                    'all_assignees' => $this->mergeUsersAndGroups($postData['assignee'], $postData['group']),
                    'documnent' => $postData['document'],
                    'document_file' => $_FILES 
                )); exit;*/
                
                $task = new Task();
                try
                {
                    $docsIds = array();
                    $doc = new InnerDocument();
                    $doc->startTransaction();
                    try
                    {
                        foreach ($_FILES as $file)
                        {
                            if ($file['name'] && $file['size'] > 0)
                            {
                                $docsIds[] =
                                    $doc->createDocument($user->id, $file['name'], $file["tmp_name"], $file["type"], $comment);
                            }
                        }
                    }
                    catch (Exception $ex)
                    {
                        $doc->rollbackTransaction();
                        throw  $ex;
                    }
                    $doc->commitTransaction();

                    $task->createTask($user->id, 
                            $postData['type'],
                            $formData['title'],
                            $formData['body'],
                            $formData['deadline'],
                            $formData['priority'],
                            $this->mergeUsersAndGroups($postData['assignee'], $postData['group']),
                            array_merge($postData['document'], $docsIds),
                            $formData['allow_reassinment_id'] ? 'true' : 'false');
                }
                catch (Exception $ex)
                {
                    var_dump($ex); exit;
                    //$this->view->errors = array('Не удалось сохранить данные', $ex->getMessage());
                    //return;
                }
                $this->_redirect('/document/index/');
            }
        }
    }

    public function listDocumentsAction()
    {
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $docs = new InnerDocument();
        $this->view->data = $docs->getInfoByOwner($user->id);
    }

    public function showEditDocumentAction()
    {
        $hash = $this->_request->getParam('hash');
        $doc = new InnerDocument();
        $docFile = new InnerDocumentFile();

        $docInfo = $doc->getInfoByHash($hash);
        $this->view->data = array(
            'doc' => $docInfo,
            'files' => $docFile->getFilesByDocument($docInfo['id']));

        if ($this->_request->isPost())
        {
            //var_dump(array('FILES' => $_FILES, 'POST' => $this->_request->getPost())); exit;
            $postData = $this->_request->getPost();
            if ($postData['update'])
            {
                $doc->update(
                        array('title' => $postData['title'], 'description' => $postData['description']),
                        "id={$docInfo['id']}");
                $this->_redirect("/document/show-edit-document/hash/{$hash}/");
            }
            else if ($postData['delete-doc'])
            {
                $doc->delete("hash={$hash}");
                $this->_redirect('/document/list-documents');
            }
            else if ($postData['new-doc'])
            {
                $file = $_FILES['doc'];
                $docFile->startTransaction();
                try
                {
                    $docFile->addDocumentFile($docInfo['owner_id'], $docInfo['id'], $file);
                }
                catch (Exception $ex)
                {
                    $docFile->rollbackTransaction();
                    $this->view->errors = array('Не удалось сохранить файл.');
                    return;
                }
                $docFile->commitTransaction();
                $this->_redirect("/document/show-edit-document/hash/{$hash}/");
            }
        }
    }

    public function addDocumentAction()
    {
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            $user = Zend_Auth::getInstance()->getStorage()->read();
            
            $doc = new InnerDocument();
            $doc->startTransaction();
            try
            {
                foreach ($_FILES as $key => $file)
                {
                    if ($file['name'] && $file['size'] > 0)
                    {
                        preg_match('/_(?P<index>\d+)$/', $key, $matches);
                        $title = $postData['title_'.$matches['index']];
                        $description = $postData['description_'.$matches['index']];
                        $doc->createDocument($user->id, 
                                $title ? $title : $file['name'],
                                $description,
                                $file);
                    }
                }
            }
            catch (Exception $ex)
            {
                $doc->rollbackTransaction();
                $this->view->errors = array('Не удалось сохранить данные', $ex->getMessage());
                return;
            }
            $doc->commitTransaction();
            $this->_redirect('/document/list-documents');
        }
    }

    public function getDocumentAction()
    {
        $hash = $this->_request->getParam('hash');
        $doc = new InnerDocumentFile();
        if ($hash) {
            $pathInfo = $doc->getFileInfoByHash($hash);
        }
        if (!$pathInfo)
        {
            $this->getResponse()->setHttpResponseCode(404);
            $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
            $this->getResponse()->setBody('Ошибка: Файл не найден.');
            $this->getResponse()->sendResponse();
        }
        else
        {
            //var_dump(InnerDocument::getFilePath($pathInfo['owner_id'], $pathInfo['media'])); exit;
            header('Content-Description: File Transfer');
            header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-Type: application/force-download');
            header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
            header("Content-Type: {$pathInfo['mime_type']}", false);
            header('Content-Disposition: attachment; filename="'.basename($pathInfo['file_name']).'";');
            header('Content-Transfer-Encoding: binary');
            header("Content-length: " . filesize(InnerDocumentFile::getFilePath($pathInfo['owner_id'], $pathInfo['media'])));
            readfile(InnerDocumentFile::getFilePath($pathInfo['owner_id'], $pathInfo['media']));
        }
        exit;
    }
}
