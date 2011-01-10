<?php
/**
 * Controller for all ajax requests
 *
 * @author marat
 */

require_once('Zend/Controller/Action.php');
require_once 'Zend/Json/Encoder.php';
require_once('jQuery.php');
require_once('ClientHelper.php');
require_once('Asr/FormHelper.php');
require_once('ClientModel.php');
require_once('PointHelper.php');
require_once 'models/InnerDocumentModel.php';
require_once 'models/InnerDocumentFileModel.php';

class AjaxController extends Zend_Controller_Action
{
	public function init()
	{
		// check is AJAX request or not
        if (!$this->getRequest() -> isXmlHttpRequest())
        {
            $this->getResponse()-> setHttpResponseCode(404)
                                -> sendHeaders();
            $this->getResponse()->setBody("Error");
            $this->getResponse()->sendResponse();
            exit;
        }
	}

    public function indexAction()
    {
        // assign to div with id = 'test' current time
        jQuery('div#test')->html(date('H:i:s'));

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    /**
     * Action for calls from abonclient pages
     */
    public function abonclientAction()
    {
        $name = $this->_request->getParam('name');

        switch ($name)
        {
            case 'check_number': //Check number for avilable to cross
                $number     = $this->_request->getParam('phone');
                $countryID  = $this->_request->getParam('country');
                $clientType = $this->_request->getParam('client_type');
                $reason     = ClientHelper::checkForBadNumber($countryID, $number);

                if ($reason)
                {
                    jQuery('div#number_test')->html('&nbsp;&nbsp;Невозможно скроссировать номер. Причина: ' . $reason);
                }
                else
                {
                    $ats = ClientHelper::getRecommendedAts($number);

                    if ( ! $ats )
                    {
                        jQuery('div#number_test')->html('&nbsp;&nbsp;Не найдено ни одной АТС для данного номера телефона');
                    }
                    else
                    {
                        $recommendedDslamID = ClientHelper::getFirstAvailableDslam($ats->id, $clientType);

                        if ( ! $recommendedDslamID )
                        {
                            jQuery('div#number_test')->html('&nbsp;&nbsp;Нет свободных портов на АТС');
                        }
                        else
                        {
                            jQuery('div#number_test')->html('&nbsp;&nbsp;Подключение возможно');
                        }
                    }
                }
                break;
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    /**
     * Action for calls from techclient pages
     */
    public function techclientAction()
    {
        $name = $this->_request->getParam('name');

        switch ($name)
        {
            case 'ats': // gets recommended ats
                $pointID = $this->_request->getParam('point');
                $countryID = $this->_request->getParam('country');
                $number = $this->_request->getParam('phone');
                $portID = $this->_request->getParam('port_id');
                $clientType = $this->_request->getParam('client_type');
                $phoneType = $this->_request->getParam('phone_type');

                // сначала сохраним телефон
                $pointModel = new EditPointModel();
                $data['pcross'] = $number;
                $data['pcross_type'] = $phoneType;
                $pointModel->update($data, "point_id = {$pointID}");

                TechHistoryHelper::addLogFromPointID($pointID, TechHistoryHelper::PHONE_CHANGE);

                /*
                 * Если уже данные были назначены, или это прямой провод
                 * то ничего предалагать не надо
                 */
                if ( $portID || $phoneType == 2)
                {
                	exit();
                }

                $reason = ClientHelper::checkForBadNumber($countryID, $number, $pointID);
                jquery('#dslam_id')->empty();
                jQuery('div#ats')->html('');
                jQuery('div#dslam')->html('');

                if ($reason)
                {
                    jQuery('div#ats')->html('&nbsp;&nbsp;Невозможно скроссировать номер. Причина: ' . $reason);
                }
                else
                {
                    $ats = ClientHelper::getRecommendedAts($number);

                    if ($ats)
                    {
                        jQuery('div#ats')->html('&nbsp;&nbsp;Рекомендуемая АТС: ' . $ats->name);
                        jQuery('#ats_id')->val($ats->id);

                        $this->fillDslams($ats->id, $clientType, $phoneType);
                    }
                    else
                    {
                        jQuery('div#ats')->html('&nbsp;&nbsp;Не найдено ни одной АТС для данного номера телефона');
                    }
                }
                break;

            case 'dslam_list': //gets dsalm list
                $atsID =  $this->_request->getParam('ats');
                $clientType = $this->_request->getParam('client_type');
                $phoneType = $this->_request->getParam('phone_type');
                $this->fillDslams($atsID, $clientType, $phoneType);
                break;

            case 'port_list': //gets port list
                $dslamID = $this->_request->getParam('dslam');
                $phoneType = $this->_request->getParam('phone_type');
                $this->fillPorts($dslamID, $phoneType);
                break;

            case 'port_info': //get port info
                $portID = $this->_request->getParam('port');
                $phoneType = $this->_request->getParam('phone_type');

                if ($portID)
                {
                    $portsModel = new Ports();
                    $port = $portsModel->fetchRow("id = {$portID}");

                    if ( ! $port )
                    {
                        $port = $portsModel->createRow();
                    }

                    jQuery('#frame_number-element')->html("<label for='frame_number'>{$port->frame_number}</label>");

                    if ($phoneType == PointHelper::PHONE_TYPE_ORDINAL)
                    {
                        $pairNumbers = $port->line_number1 . '/' . $port->line_number2;
                    }
                    else
                    {
                        $pairNumbers = $port->line_number2;
                    }

                    jQuery('#pair_number-element')->html("<label for='pair_number'>{$pairNumbers}</label>");
                    $statusText = $portsModel->getStatusLabel($port->status);
                    jQuery('#port_status-element')->html("<label for='port_status'>{$statusText}</label>");
                }
                break;
            case 'clear_info':
            	$pointID = $this->_request->getParam('point');

            	if ( ! $pointID )
            	{
            		exit();
            	}

            	$data['ats_id'] = new Zend_Db_Expr('null');
            	$data['dslam_id'] = new Zend_Db_Expr('null');
            	$data['port_id'] = new Zend_Db_Expr('null');
            	$pointModel = new EditPointModel();
            	$pointModel->update($data, "point_id = {$pointID}");
            	TechHistoryHelper::addLogFromPointID($pointID, TechHistoryHelper::DETACH_PORT_ACTION);

            	jQuery('#ats_id')->val('');
            	jQuery('#dslam_id')->val('');
            	jQuery('#port_id')->val('');
            	jQuery('#frame_number-element')->html('');
            	jQuery('#pair_number-element')->html('');
            	break;
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    private function fillPorts($dslamID, $phoneType)
    {
        jquery('#port_id')->empty();

        if($dslamID)
        {
            $portsOptions = ClientHelper::getPortList($dslamID);
            $port = ClientHelper::getFirstAvailablePort($dslamID);

            jquery('#port_id')->html(FormHelper::getSelectOptions($portsOptions, $port->id, true));
            jQuery('div#port')->html('&nbsp;&nbsp;Рекомендуемый порт: ' . $port->number);
            jQuery('#frame_number-element')->html("<label for='frame_number'>{$port->frame_number}</label>");

            if ($phoneType == PointHelper::PHONE_TYPE_ORDINAL)
            {
                $pairNumbers = $port->line_number1 . '/' . $port->line_number2;
            }
            else
            {
                $pairNumbers = $port->line_number2;
            }

            $portsModel = new Ports();
            jQuery('#pair_number-element')->html("<label for='pair_number'>{$pairNumbers}</label>");
            $statusText = $portsModel->getStatusLabel($port->status);
            jQuery('#port_status-element')->html("<label for='port_status'>{$statusText}</label>");
        }
    }

    public function getDslamsAction()
    {
        $atsID =  $this->_request->getParam('ats');

        $dslamListModel = new DslamList();

        jQuery('#dslam_id')->empty();

        if ($atsID)
        {
        	$dslamModel = new DslamList();
        	$dslamOptions = $dslamModel->getOptionsWithIps("ats_id = {$atsID}");
        	jQuery('#dslam_id')->html(FormHelper::getSelectOptions($dslamOptions, null, true));
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function getSwitchByAtsIdAction()
    {
        $atsID =  $this->_request->getParam('ats_id');

        $switchModel = new SwitchModel();

        jQuery('#switch_id')->empty();

        if ($atsID)
        {
        	$switchModel = new SwitchModel();
        	$switchOptions = $switchModel->getOptionsForSwitch("ats_id = {$atsID}");
        	jQuery('#switch_id')->html(FormHelper::getSelectOptions($switchOptions, null, true));
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    private function fillDslams($atsID, $clientType, $phoneType)
    {
        $dslamListModel = new DslamList();

        jquery('#dslam_id')->empty();
        jQuery('div#dslam')->html('');

        if ($atsID)
        {
            $dslamOptions = ClientHelper::getDslamList($atsID, $clientType);
            $recommendedDslamID = ClientHelper::getFirstAvailableDslam($atsID, $clientType);
            jquery('#dslam_id')->html(FormHelper::getSelectOptions($dslamOptions, $recommendedDslamID, true));

            if ($recommendedDslamID)
            {
                jQuery('div#dslam')->html('&nbsp;&nbsp;Рекомендуемый комутатор: ' . $dslamOptions[$recommendedDslamID]);

                $this->fillPorts($recommendedDslamID, $phoneType);
            }
        }
    }
    /*
     * Данный метод позволяет проверить расчетные счета при заведении
     * нового клиента
     */
    public function varifysattlementAction()
    {
        $arr = explode(',', $this->_request->getParam('arr'));

        $rschetModel = new ClientModel();

        jQuery('div#msg_rschet')->html('');
        $messages = '';
        $connector = '';
		$midleconnector = '<br/>';
        for($i =0; $i<count($arr); $i++)
        {
            $row_rschet = $rschetModel->varifyRschet($arr[$i]);

            if($row_rschet)
            {
                $messages .= $connector. '*номер расчетного счета уже есть в БД: '. $arr[$i];
                $connector = "<br/>";
            }
            $messages .= $midleconnector. ClientHelper::checkRshetNumber($arr[$i]);
        }

        jQuery('div#msg_rschet')->html($messages);
        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    /**
     * Ajax reponder from tech history pages calls
      */
    public function techhistoryAction()
    {
        $name = $this->_request->getParam('name');

        switch ($name)
        {
            case 'dslam_list':
                $ats = $this->_request->getParam('ats');
                $dslamOptions = ClientHelper::getDslamList($ats);
                jQuery('#dslam')->html(FormHelper::getSelectOptions($dslamOptions, false, true));
                break;

            case 'port_list':
                $dslam = $this->_request->getParam('dslam');

                if ( ! $dslam )
                {
                    $dslam = "0";
                }

                $portsModel = new Ports();
                $portsOptions = $portsModel->getPortsNumbers("dslam_id = {$dslam}");

                jQuery('#port')->html(FormHelper::getSelectOptions($portsOptions, false, true));
                break;
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function viewtarifsAction()
    {
    	$is_stream = $this->_request->getParam('is_face');

		$kitModel = new KitModel();
		$allTarifs = $kitModel->getTarifsByClientTypeID($is_stream);

		$htmlResponse = "<select size=\"1\" name=\"tarif_id\"><option selected disabled>Выберите тариф</option>";
		foreach($allTarifs as $item)
		{
			$htmlResponse .= "
				<option value=\"$item[tarif_id]\">$item[tarif_name]</option>
			";
		}

		jQuery('#tarif')->html($htmlResponse);
		$this->getResponse()->appendBody(jQuery::getResponse());
		exit;
    }

    public function addasrtypesAction()
    {
    	$rovd_name 		= $this->_request->getParam('rovd');
        $bank_name 		= $this->_request->getParam('bank');
    	$client_type_id = $this->_request->getParam('ctype');

    	$clientModel = new ClientModel();

        if($client_type_id == 1 and $rovd_name != "")
        {
            $clientModel->addRovd($rovd_name);
            jQuery('#rovd_label')->html('РУВД добавлено');
        }
        elseif($client_type_id == 0 and $bank_name != "")
        {
            $clientModel->addBank($bank_name);
            jQuery('#bank_label')->html('Банк добавлен');
        }

    	$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function showPointInfoAction()
    {
    	$pointID = $this->_request->getParam('point_id');
    	$table = $this->_request->getParam('table');
    	$pointModel = new EditPointModel();
    	$info = $pointModel->getInfo($pointID, $table);

    	jQuery('#client_name-element')->html("<label for='client_name'>{$info['client_name']}</label>");
    	jQuery('#pcross-element')->html("<label for='pcross'>{$info['pcross']}</label>");
    	jQuery('#point_id')->val($info['point_id']);

    	$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function showDslamsAction()
    {
    	$dslamModel = new DslamList();

    	$atsID = $this->_request->getParam('ats');

		$dslamData = $dslamModel->fetchAllByAts($atsID);

		$htmlcontentDslamList = "
		<form action='/accident/mark-dslam' name='frm1' method='post'>
	        <table class='list' cellpadding='3' cellspacing='0'>
            <tr>
            	<th class='list_head'>N</th>
				<th class='list_head'>IP адрес</th>
				<th class='list_head'>Название</th>
				<th class='list_head'>АТС</th>
				<th class='list_head'>Комментарии</th>
				<th class='list_head'>Действие</th>
            </tr>
		";
		$counter = 0;
		foreach($dslamData as $key => $value)
		{
			$counter++;

			// TR
			$htmlcontentDslamList .= "
				<tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
			";
			// TD
			$htmlcontentDslamList .= "
                <td class='list_row'>$counter</td>
				<td class='list_row'>$value[ip_address]</td>
				<td class='list_row'>$value[name]</td>
				<td class='list_row'>$value[ats_id]</td>
				<td class='list_row'>
					<input type='text' name='{$value[id]}[notes]' size='25' />
				</td>
				<td class='list_row'>
					<input type='checkbox' name='{$value[id]}[check]' />
					<input type='hidden' value='$value[id]' name='{$value[id]}[dslam]' id='dslam_$value[id]' />
					<input type='hidden' value='$value[ats_id]' name='{$value[id]}[ats_id]' id='dslam_$value[id]' />
				</td>
			";
			$htmlcontentDslamList .= "
				</tr>
			";
		}
		$htmlcontentDslamList .= "
			</table>
			<br />
			<center><input type='submit'  value='отметить' /></center>
			</form>
		";
		jQuery('#traced')->html($htmlcontentDslamList);
		$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function removeSchetAction()
    {
        $rschetID = $this->_request->getParam('rid');

        if($rschetID != "")
        {
            $rschetModel = new Rschet();

            $result = $rschetModel->removeAccountRschet($rschetID);
        }
        jQuery('#info-rschet')->html("<label>Р/с удален.</label>");

    	$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function removeDocumentAction()
    {
        $id = $this->_request->getParam('id');
        $hash = $this->_request->getParam('hash');
        $doc = new InnerDocument();
        $doc->delete(array('id' => $id, 'hash' => $hash));

        $this->getResponse()->setBody(Zend_Json_Encoder::encode(array('status'=>'OKay', 'id'=>$id)));
        $this->getResponse()->sendResponse();
        exit;
    }

    public function modifyFileAction()
    {
        $verb = $this->_request->getParam('verb');
        $fileHash = $this->_request->getParam('hash');
        $docId = $this->_request->getParam('doc');

        /*$this->getResponse()->setBody(Zend_Json_Encoder::encode(array(
            'verb' => $verb,
            'fileHash' => $fileHash,
            'docId' => $docId
        )));
        $this->getResponse()->sendResponse();
        exit;*/

        $file = new InnerDocumentFile();
        if ($verb == 'delete')
        {
            $result = $file->removeByHash($docId, $fileHash);
        }
        else if ($verb == 'activate')
        {
            $result = $file->setActiveByHash($docId, $fileHash);
        }

        $this->getResponse()->setBody(Zend_Json_Encoder::encode(array('status'=>'OKay', 'doc'=>$result)));
        $this->getResponse()->sendResponse();
        exit;
    }
}