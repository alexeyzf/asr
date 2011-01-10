<?php
/**
 * Controller for admin users
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('AdminUser.php');
require_once('AdminGroup.php');
require_once('AdminModule.php');
require_once('AdminUserGroup.php');
require_once('PasswordConfirmationValidator.php');
require_once('forms/Adminuser.php');

class AdminUserController extends BaseController
{
    /**
     * Action for list of users
     */
    public function indexAction()
    {
        $adminUser = new AdminUser();
        $this->view->users = $adminUser->fetchAllNotDeleted();

        $adminGroup = new AdminGroup();
        $dbGroups = $adminGroup->fetchAll();
        foreach ($dbGroups as $dbGroup)
        {
            $groups[$dbGroup->id] = $dbGroup->name;
        }

        $adminUserGroup = new AdminUserGroup();
        $dbUserGroups = $adminUserGroup->fetchAll();
        foreach ($dbUserGroups as $dbUserGroup)
        {
            $userGroups[$dbUserGroup->user_id][] =  $groups[$dbUserGroup->group_id];
        }
        $this->view->userGroups = $userGroups;

        $adminModule = new AdminModule();
        $dbModules = $adminModule->fetchAllNotDeleted();
        $modules = array();
        foreach ($dbModules as $module)
        {
            $modules[$module->name] = $module->label;
        }
        $this->view->modules = $modules;
    }

    /**
     * Action for modify user
     */
    public function modifyAction()
    {
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $id = $this->_request->getPost('id');

            $userGroups =  $this->_request->getPost('user_groups');
            unset($userGroups['newCOUNTER']);

            $adminUser = new AdminUser();
            $adminUserGroup = new AdminUserGroup();
            $adminGroup = new AdminGroup();

            $form = $this->createForm();

            if ( $form->isValid($formData) )
            {
                $values = $form->getValues();
                $values['password'] = md5($values['password']);

                try
                {
                    $adminUser->startTransaction();
                    $id = $adminUser->saveChanges($values, $id);
                    $adminUserGroup->deleteUserGroups($id);
                    $adminUserGroup->insertUserGroups($userGroups, $id);
                    $adminUser->commitTransaction();
                }
                catch(Exception $ex)
                {
                    $adminUser->rollbackTransaction();
                    print $ex;
                    exit;
                }

                $this->_redirect('/admin-user');
            }
            else
            {
                $groups = $adminGroup->fetchAllNotDeleted();
                $this->view->form = $form;
                $this->view->userGroups = $userGroups;
                $this->view->groups = $groups;

                if ($id)
                {
                    $this->view->userID = $id;
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
            $adminUser = new AdminUser();
            $adminUserGroup = new AdminUserGroup();
            $adminGroup = new AdminGroup();
            $groups = $adminGroup->fetchAllNotDeleted();
            $userGroups = array();

            if ($id)
            {
                $this->view->userID = $id;
                $this->view->isNew = false;

                $user = $adminUser->fetchRecordByID($id);
                $dbUserGroups = $adminUserGroup->fetchUserGroups($id);

                foreach ($dbUserGroups as $dbUserGroup)
                {
                    $userGroups[$dbUserGroup->id] =  $dbUserGroup->group_id;
                }
            }
            else
            {
                $this->view->isNew = true;
                $user = $adminUser->createRow();
            }

            $form = $this->createForm($user);
            $this->view->form = $form;
            $this->view->userGroups = $userGroups;
            $this->view->groups = $groups;
        }
    }

    /**
     * Creates Zend form for modify page
     *
     * @param Zend_Db_Table_Row_Abstract $user
     * @return Zend_Form
     */
    private function createForm($user = null)
    {
        if ( ! $user )
        {
            $adminUser = new AdminUser();
            $user = $adminUser->createRow();
        }

        $values = $user->toArray();

        $adminModule = new AdminModule();
        foreach ($adminModule->fetchAllNotDeleted() as $module)
        {
            $homePageOptions[$module->name] = $module->label;
        }

        $values['home_page_options'] = $homePageOptions;

        $form = new Form_Adminuser();
        $form->populate($values);

        return $form;
    }

    /**
     * Action for delete user
     */
    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $adminUser = new AdminUser();
        $adminUser->markToDelete($id);

        $this->_redirect('/admin-user');
    }
}
?>