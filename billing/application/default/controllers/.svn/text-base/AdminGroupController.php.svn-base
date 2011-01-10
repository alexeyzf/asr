<?php
/**
 * Controller for Admin groups
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('AdminGroup.php');
require_once('AdminModule.php');
require_once('AdminAction.php');
require_once('AdminGroupPriv.php');
require_once('forms/Admingroup.php');
require_once('Zend/Form.php');

class AdminGroupController extends BaseController
{
    /**
     * Action for list of groups
     */
    public function indexAction()
    {
         $adminGroup = new AdminGroup();
         $this->view->groups = $adminGroup->fetchAllNotDeleted(null, 'name');
    }

    /**
     * Action for modify group
     */
    public function modifyAction()
    {
        $adminModule = new AdminModule();
        $adminAction = new AdminAction();
        $adminGroup = new AdminGroup();
        $adminGroupPriv = new AdminGroupPriv();

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $privs = $this->_request->getPost('privileges');

            $form = new Form_Admingroup();
            $id = $this->_request->getPost('id');

            if ( $form->isValid($formData) )
            {
                try
                {
                    $adminGroup->startTransaction();
                    $id = $adminGroup->saveChanges($form->getValues(), $id);
                    $adminGroupPriv->deleteGroupPrivs($id);

                    foreach ($privs as $actionID => $moduleID)
                    {
                        $group_privs = array();
                        $group_privs['group_id'] = $id;
                        $group_privs['module_id'] = $moduleID;
                        $group_privs['action_id'] = $actionID;
                        $adminGroupPriv->insert($group_privs);
                    }

                    $adminGroup->commitTransaction();
                }
                catch(Exception $ex)
                {
                    $adminGroup->rollbackTransaction();
                    print $ex;
                    exit;
                }

                $this->_redirect('/admin-group');
            }
            else
            {
                $this->view->modules = $adminModule->fetchAllNotDeleted();
                $this->view->actions = $adminAction->fetchAllNotDeleted();
                $this->view->privs = $privs;
                $this->view->form = $form;

                if ($id)
                {
                    $this->view->groupID = $id;
                    $this->view->isNew = false;
                }
                else
                {
                     $this->view->isNew = true;
                }
            }
        }
        else
        {
            $id = $this->_request->getParam('id');
            $adminGroup = new AdminGroup();

            if ($id)
            {
                $this->view->groupID = $id;
                $this->view->isNew = false;
                $group = $adminGroup->fetchRecordByID($id);

                $dbPrivs = $adminGroupPriv->fetchGroupPrivs($id);
                $privs = array();

                foreach ($dbPrivs as $dbPriv)
                {
                    $privs[$dbPriv['action_id']] = 1;
                }

                $this->view->privs = $privs;

            }
            else
            {
                $this->view->isNew = true;
                $group = $adminGroup->createRow();
            }

            $form = new Form_Admingroup();
            $form->populate($group->toArray());

            $this->view->form = $form;
            $this->view->modules = $adminModule->fetchAllNotDeleted('', 'label');
            $this->view->actions = $adminAction->fetchAllNotDeleted();

        }
    }

    /**
     * Action for delete group
     */
    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $adminGroup = new AdminGroup();
        $adminGroup->markToDelete($id);

        $this->_redirect('/admin-group');
    }
}
?>
