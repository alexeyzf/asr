<?php
/**
 * Conntroller for tech phone hubs
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('PhoneHubList.php');
require_once('forms/Techphonehub.php');

class TechPhonehubController extends BaseController
{
    /**
     * Action for modify page
     */
    public function modifyAction()
    {
        $phoneHubModel = new PhoneHubList();
        $id = $this->_request->getParam('id');
        $atsID = $this->_request->getParam('ats_id');

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $form = $this->createForm();

            if ( $form->isValid($formData) )
            {
                $phoneHubModel->saveChanges($form->getValues(), $id);

                $atsID = $this->_request->getParam('ats_id');
                $this->_redirect("/tech-ats/modify/id/{$atsID}");
            }
            else
            {
                $this->view->isNew = $id ? true : false;
                $this->view->form = $form;
                $this->view->atsID = $atsID;
            }
        }
        else
        {
            if ($id)
            {
                $phoneHubInfo = $phoneHubModel->fetchRecordByID($id);
                $this->view->isNew = false;
            }
            else
            {
                $phoneHubInfo = $phoneHubModel->createRow();
                $this->view->isNew = true;
            }

            $this->view->form = $this->createForm($phoneHubInfo);
            $this->view->atsID = $atsID;
        }
    }

    /**
     * Creates Zend_Form for modify action
     *
     * @param Zend_Db_Table_Row_Abstract $phoneHubInfo
     * @return Zend_Form
     */
    private function createForm($phoneHubInfo = null)
    {
        if ( ! $phoneHubInfo )
        {
            $phoneHubModel = new PhoneHubList();
            $phoneHubInfo = $phoneHubModel->createRow();
        }

        $form = new Form_Techphonehub();
        $form->populate($phoneHubInfo->toArray());

        return $form;
    }

    /**
     * Action for delete phone hub
     */
    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $phoneHubModel = new PhoneHubList();
        $phoneHubModel->markToDelete($id);

        $this->_redirect('/tech-phonehub');
    }
}