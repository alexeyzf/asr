<?php
/**
 * Controller for cover zone
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('Cities.php');
require_once('forms/Coverzone.php');

class CoverzoneController extends BaseController
{
    /**
     * Action for list page
     */
    public function indexAction()
    {
        $atsID = $this->_request->getParam('ats_id');

        $citiesModel = new Cities();

        if ( $this->_request->isPost() )
        {
            $data = $this->_request->getParam('cities');

            foreach ($data as $cityID => $cityData)
            {
                if ( ! $cityData['is_enabled'] )
                {
                    $cityEnabled = 0;
                }
                else
                {
                    $cityEnabled = 1;
                }

                $cityData = array();
                $cityData['is_enabled'] = $cityEnabled;
                $citiesModel->saveChanges($cityData, $cityID);
            }
        }

        $this->view->cities = $citiesModel->fetchAll();
        $this->view->atsID = $atsID;
    }

    /**
     * Action for modify page
     */
    public function modifyAction()
    {
        $citiesModel = new Cities();
        $id = $this->_request->getParam('id');
        $atsID = $this->_request->getParam('ats_id');

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $form = $this->createForm();

            if ( $form->isValid($formData) )
            {
                $data['id'] = $data['code'];
                $citiesModel->saveChanges($data, $id);

                $this->_redirect("/coverzone/index/ats_id/{$atsID}");
            }
            else
            {
                $this->view->form = $form;
                $this->view->cityID = $id;
            }
        }
        else
        {
            if ($id)
            {
                $cityInfo = $citiesModel->fetchRecordByID($id);
            }
            else
            {
                $cityInfo = $citiesModel->createRow();
                $this->view->isNew = true;
            }

            $this->view->form = $this->createForm($cityInfo);
            $this->view->cityID = $id;
        }
    }

    /**
     * Creates Zend_Form for modify action
     *
     * @param Zend_Db_Table_Row_Abstract $cityInfo - City data to populate form with
     * @return Zend_Form
     */
    private function createForm($cityInfo = null)
    {
        if ( ! $cityInfo )
        {
            $citiesModel = new Cities();
            $cityInfo = $citiesModel->createRow();
        }

        $values = $cityInfo->toArray();
        $values['code'] = $values['id'];

        $form = new Form_Coverzone();
        $form->populate($values);
        
        return $form;
    }

    /**
     * Action for delete cover zone
     */
    public function deleteAction()
    {
        $id = intval($this->_request->getParam('id'));

        if ($id)
        {
            $citiesModel = new Cities();
            $citiesModel->delete('id = ' . $id);
        }

        $this->_redirect('/techats');
    }
}