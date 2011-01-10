<?php
require_once ('BaseController.php');

class BlackListController extends BaseController
{

    public function indexAction()
    {
    	$this->view->infos  = MessageHelper::getAllInfos();
		$translator 		= Zend_Registry::get('Zend_Translate');

		$blackModel = new BlackListModel();

		$result = $blackModel->getBlackList();

		if($this->_request->isPost())
		{
			$data = $this->_request->getPost();
			// INSERT
			$resultSQL = $blackModel->addNewPost($data);

			if($resultSQL == 1)
			{
				MessageHelper::addInfo('blacklist-info', $translator->_('black_listAlreadyUsedByNumber'));
				$this->_redirect('/black-list');
			}
			else
			{
				MessageHelper::addInfo('blacklist-info', $translator->_('black_list_newPostAdded'));
				$this->_redirect('/black-list');
			}
		}

		$this->view->data = $result;
    }

    public function deletePostAction()
    {
		$translator 		= Zend_Registry::get('Zend_Translate');

    	$ID = $this->_request->getParam('id');

    	if($ID)
    	{
			$model  = new BlackListModel();
			$result = $model->deleteBlackList($ID);

			MessageHelper::addInfo('blacklist-info', $translator->_('black_list_post_deleted'));
			$this->_redirect('/black-list');
    	}
    	else
    	{
    		$this->_redirect('/black-list');
    	}
    }
}