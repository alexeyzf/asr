<?php
/**
 * Controller for switch pages
 *
 * @author marat
 */

require_once ('BaseController.php');
require_once ('forms/SwitchForm.php');
require_once ('forms/SwitchPortForm.php');
require_once ('forms/TransportForm.php');
require_once ('jQuery.php');

class SwitchController extends BaseController
{
	/**
	 * Showes list of switches
	 */
	public function indexAction()
	{
		$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();

		$switchModel = new SwitchModel();
		$switches = $switchModel->getList();
		$this->view->switches = $switches;
	}

	public function editAction()
	{
		$ID = $this->_request->getParam('id');

		$form = new SwitchForm();
		$this->view->form = $form;
		$this->view->swid = $ID;

		if ( $this->_request->isPost() )
		{
			$data = $this->_request->getPost();

			if ( $form->isValid($data) )
			{
				$formData = $form->getValues();

				$switchModel = new SwitchModel();
				$ID = $switchModel->saveChanges($formData, $ID);

				$switchesTypesModel = new SwitchesTypesModel();
				$typeInfo = $switchesTypesModel->getByID($formData['switch_type_id']);

				$switchPortModel = new SwitchPortsModel();
				$switchPortModel->fillPorts($ID, $typeInfo['ports_count']);

				$this->_redirect('/switch');
			}
		}
		elseif ($ID)
		{
			$switchModel = new SwitchModel();
			$switch = $switchModel->getSwitchByID($ID);
			$form->populate($switch);
		}
	}

	public function editPortAction()
	{
		$ID = $this->_request->getParam('id');

		$switchPortModel = new SwitchPortsModel();

		$result = $switchPortModel->verifyIsTransportPort($ID);

		if($result)
		{
			//var_dump($result);
			//exit();
			$transportForm = new TransportForm();
			$transportForm->populate($result[0]);
			$this->view->data = 'dddd';
			$this->view->form = $transportForm;
			$this->render('port-is-transport');
		}

		$form = new SwitchPortForm();
		$this->view->form = $form;

		if ( $this->_request->isPost() )
		{
			$data = $this->_request->getPost();

			if($data['equipment_type'] == "transport")
			{
				$portID = $switchPortModel->savePort($data, $ID);
			}
			else
			{
				if ( $form->isValid($data) )
				{
					$formData = $form->getValues();

					$portID = $switchPortModel->savePort($data, $ID);

					$this->_redirect('/switch');
				}
			}
		}
		elseif ($ID)
		{
			$switchPortModel = new SwitchPortsModel();
			$switchPort = $switchPortModel->getPort($ID);
			$form->populate($switchPort);
		}
	}

	public function addTypeAction()
	{
		$name = $this->_request->getParam('name');
		$portsCount = $this->_request->getParam('ports_count');

		$switchesTypesModel = new SwitchesTypesModel();
		$ID = $switchesTypesModel->add($name, $portsCount);

		$options = $switchesTypesModel->getOptions();

		jQuery('#switch_type_id')->html(FormHelper::getSelectOptions($options, $ID));
		$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
	}

	public function deleteSwitchAction()
	{
		$translator = Zend_Registry::get('Zend_Translate');

		if($this->_request->getPost())
		{
			$swid = $this->_request->getPost('switch_id');
			if($swid)
			{
				$switchModel = new SwitchModel();
				$result = $switchModel->deleteSW($swid);

				MessageHelper::addInfo('switch-info', $translator->_('switchDeleted'));

				$this->_redirect('/Switch/index');
			}
		}
	}

	public function clearTransportPortAction()
	{
		 if($this->_request->isPost())
		 {
		 	$portID = $this->_request->getPost('switchbind_id');

			$switchModel = new SwitchModel();
			$result 	 = $switchModel->clearTransportPortByID($portID);
			$this->_redirect('/Switch');
		 }
	}
}