<?
/**
 * ClientRecalculationController
 * 
 * Controller for client recalculation pages
 * 
 * @author marat
 */

require_once('BaseController.php');
require_once('forms/ClientRecalculationHistory.php');

class ClientRecalculationController extends BaseController 
{
	public function indexAction() 
	{
		$clientID = $this->_request->getParam('client_id');
		$form = new Form_ClientRecalculationHistory();
		
		$this->view->form = $form;
		$this->view->clientID = $clientID;
		
		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();
			
			if ($form->isValid($postData))
			{
				$startDate = $form->getValue('startdate');
				$endDate = $form->getValue('enddate');
				
				$recalcTasksModel = new Recalcs();
				$this->view->history = $recalcTasksModel->getRecalculations($clientID, $startDate, $endDate);
			}
		}
	}
}