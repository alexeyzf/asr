<?php
/**
 * Base class of controllers
 */

require_once ('Zend/Controller/Action.php');
require_once ('Asr/FormHelper.php');

class BaseController extends Zend_Controller_Action
{
    function init()
    {

        $this->view->baseUrl = $this->_request->getBaseUrl();

        if ( ! Zend_Auth::getInstance()->getIdentity() )
        {
            $this->_redirect('/auth/login');
        }

        $privs = $_SESSION['privs'];

        if ( ! is_array($privs) )
        {
            return;
        }

        $moduleName = strtolower($this->_request->getControllerName());
        $actionName = strtolower($this->_request->getActionName());

        if ($moduleName == 'error' || $moduleName == 'index')
        {
            return;
        }

        foreach ($privs as $priv)
        {
            if (strtolower($priv['module_name']) == $moduleName
                && strtolower($priv['action_name']) == $actionName)
            {
                return;
            }
        }

        $this->_redirect("/auth/priv/c/{$moduleName}/a/{$actionName}");
    }
}