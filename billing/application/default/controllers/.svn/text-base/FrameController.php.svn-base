<?php
/**
 * Controller for frame pages
 * 
 * @author marat
 */

require_once ('Zend/Controller/Action.php');

class FrameController extends Zend_Controller_Action
{
	public function showReportAction() 
	{
		$portsModel = new Ports();
		$dslamTypesModel = new DslamTypes();
		$atsModel = new AtsList();
		$dslamtypes = $dslamTypesModel->getOptions();
		$atsList = $atsModel->getOptions();
		$this->view->atsList = $atsList;
		
		$atsID = $this->_request->getParam('ats_id');
		$this->view->atsID = $atsID;
		
		if ($atsID)
		{
			$data = $portsModel->getPortList(null, $atsID, 'ats_id, frame_number, line_number1');
			$frames = array();
			
			foreach ($data as $item)
			{
				$item['dslam_type'] = $dslamtypes[ $item['dslam_type_id'] ];
				$item['ats_name'] = $atsList[ $item['ats_id'] ];
				$item['dslam'] = $item['dslam_name'] . ' (' . $item['dslam_ip'] . ') Тип: ' . $item['dslam_type'];
				$item['port_status'] = $portsModel->getStatusLabel($item['status']);
				
				$key = $item['ats_id'] . '/' . $item['frame_number'];
				
				if ( ! is_array($frames[$key]) )
				{
					$frames[$key] = array();
				}
				
				if ( ! $item['point_id'] )
				{
					$item['client_name'] = $item['port_status'];
					$item['number'] = $item['port_status'];
					$item['dslam'] = $item['port_status'];
				}
				
				$clientName = $item['client_name'];
				$portNumber = $item['number'];
				$item['rowspan'] = 1;
				$notPrintNext = false; 
				
				if ($item['pcross_type'] == 1)
				{
					$item['rowspan'] = 2;
					$notPrintNext = true;
				}
				elseif ($item['pcross_type'] == 2)
				{
					$item['client_name'] = '';
					$item['number'] = '';
				}
				
				$item['pair'] = $item['line_number1'];
				array_push($frames[$key], $item);
				
				$item['client_name'] = $clientName;
				$item['pair'] = $item['line_number2'];
				$item['number'] = $portNumber;
				if ($notPrintNext)
				{
					$item['noprint'] = true;	
				}
				array_push($frames[$key], $item);
			}
			
			$this->view->frames = $frames;
		}
	}
}