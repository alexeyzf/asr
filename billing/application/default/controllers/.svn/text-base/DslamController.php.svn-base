<?php
require_once ('DslamList.php');
require_once ('Asr/FormHelper.php');

class DslamController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->view->infos  = MessageHelper::getAllInfos();
    }

    public function startAction()
    {
        $data = $this->_request->getPost();

        $auth = Zend_Auth::getInstance();
        $authData = $auth->getStorage()->read();
        //$tranlsate = Zend_Registry::get('Zend_Translate');

        $model = new DslamList();
        $rebootData = $model->DslamRebotList(trim($data['need_ip']));

        for($i = 0; $i <count($rebootData); $i++)
        {
                $model->WriteTaskFroReboot($rebootData[$i], trim($data['need_ip']));
        }

        //MessageHelper::addInfo('dslam_reboot_done', $tranlsate->_('dslamRebootDone'));

        $this->_redirect('/Dslam/index');
    }
}