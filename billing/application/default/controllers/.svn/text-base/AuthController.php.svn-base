<?php

/**
 * AuthController
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';
require_once('Zend/Filter/StripTags.php');
require_once('Zend/Auth/Adapter/DbTable.php');
require_once('AdminGroupPriv.php');
require_once('AdminModule.php');
require_once('AdminAction.php');

class AuthController extends Zend_Controller_Action
{
    /**
     * The default action - show the home page
     */
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
    }

    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function loginAction()
    {
        $this->_helper->layout->setLayout('login');

        $this->view->message = '';

        if ($this->_request->isPost())
        {
            //collect the data from the user
            $f = new Zend_Filter_StripTags();
            $username = $f->filter($this->_request->getPost('username'));
            $password = $f->filter($this->_request->getPost('password'));

            if (empty($username))
            {
                $this->view->message = 'Please provide a username.';
            }
            else
            {
                //setup Zend_Auth adapter for a database table
                $dbAdapter = Zend_Registry::get('dbAdapter');
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter, 'users', 'login', 'password', 'MD5(?)');

                //set the input credential values
                //to authenticate against
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);

                //do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                $adminGroupPriv = new AdminGroupPriv();

                if ($result->isValid())
                {
                    // success: store database row to auth's storage
                    // system. (Not the password though!)
                    $data = $authAdapter->getResultRowObject(array('id', 'first_name', 'last_name', 'home_page', 'is_deleted', 'country'));

                    if ($data->is_deleted == true)
                    {
                    	// failure: clear database row from session
                    	$this->view->message = 'Ошибка авторизации.';
                    	$this->view->title = "Авторизация";
        				$this->render();
        				return;
                    }

                    $_SESSION['privs'] = $adminGroupPriv->getPrivilegesByUserID($data->id);
                    $homePage = $data->home_page;
                    $auth->getStorage()->write($data);

                    $this->_redirect('/' . $homePage);
                }
                else
                {
                    // failure: clear database row from session
                    $this->view->message = 'Ошибка авторизации.';
                }
            }
        }
        $this->view->title = "Авторизация";
        $this->render();
    }

    function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    function privAction()
    {
        $fromController = $this->_request->getParam('c');
        $fromAction = $this->_request->getParam('a');

        $moduleModel = new AdminModule();
        $this->view->controllerLabel = $moduleModel->getLabelByName($fromController);

        $actionModel = new AdminAction();
        $this->view->actionLabel = $actionModel->getLabelByName($fromController, $fromAction);
    }
}