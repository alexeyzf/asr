<?php
/**
 * Tech client modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/Label.php');

class Form_Techclient extends Zend_Form
{
    public function init()
    {
        $this->setMethod('POST');

        $loginElement = new Form_Element_Label('u_login');
        $loginElement->setLabel('Логин:');

        $clientElement = new Form_Element_Label('client_name');
        $clientElement->setLabel('Абонент:');

        $lineOwnerElement = new Form_Element_Label('pcross_owner');
        $lineOwnerElement->setLabel('Владелец телефона:');

        $clientTypeElement = new Form_Element_Label('typename');
        $clientTypeElement->setLabel('Тип клиента:');

        $serviceTypeElement = new Form_Element_Label('service_name');
        $serviceTypeElement->setLabel('Тип услуги:');

        $addressElement = new Form_Element_Label('connect_address');
        $addressElement->setLabel('Адрес установки:');

        $dateAgreeElement = new Form_Element_Label('dateagree');
        $dateAgreeElement->setLabel('Дата договора:');

        $dateCrossElement = new Form_Element_Label('crossdate');
        $dateCrossElement->setLabel('Дата кроссировки:');

        $managerElement = new Form_Element_Label('manager_name');
        $managerElement->setLabel('Менеджер:');

        $engineerElement = new Form_Element_Label('engineer_name');
        $engineerElement->setLabel('Инженер:');

	$country_id = new Zend_Form_Element_Hidden('country_id');


        $phoneElement = new Zend_Form_Element_Text('pcross');
        $phoneElement->setLabel('Телефон:');

        $phoneTypeElement = new Zend_Form_Element_Select('pcross_type',
                array('disableLoadDefaultDecorators' => true));
        $phoneTypeElement->addDecorators(array(
                array('ViewHelper'),
                array('Errors'),
                array('Description', array('tag' => 'p', 'class' => 'description')),
                array('HtmlTag', array('tag' => 'span')),
                array('Label', array('tag' => 'span')),
        ));
        $phoneTypeElement->addMultiOption('1', 'Телефон');
        $phoneTypeElement->addMultiOption('2', 'Прямой провод');

        $atsElement = new Zend_Form_Element_Select('ats_id');
        $atsElement->setLabel('АТС:')
            ->setAttrib('onchange', 'getDslamList()')
            ->setAttrib('style', 'width: 200px')
            ->setRequired()
            ->addMultiOption('', '');

        $dslamElement = new Zend_Form_Element_Select('dslam_id');
        $dslamElement->setLabel('Коммутатор:')
            ->setAttrib('onchange', 'getPorts()')
            ->setAttrib('style', 'width: 200px')
            ->setRequired();

        $portElement = new Zend_Form_Element_Select('port_id');
        $portElement->setLabel('Порт:')
             ->setAttrib('onchange', 'getPortInfo()')
             ->setAttrib('style', 'width: 200px')
             ->setRequired();

        $isPerekross = new Zend_Form_Element_Select('isPerekross');
        $isPerekross->setLabel('Смена номера:')
             ->setAttrib('style', 'width: 200px');
        $isPerekross->addMultiOption('0', 'пусто');
        $isPerekross->addMultiOption('1', 'перекрос');
        $isPerekross->addMultiOption('2', 'смена номера (логин)');
        $isPerekross->addMultiOption('3', 'смена номера (Без смены логин)');

        $hiddenClientTypeElement = new Zend_Form_Element_Hidden('client_type_id');
        $hiddenClientTypeElement->setValue($clientInfo['client_type_id']);


        $faceid = new Zend_Form_Element_Hidden('faceid');

        $frameNumberElement = new Form_Element_Label('frame_number');
        $frameNumberElement->setLabel('Номер рамки:');

        $pairNumberElement = new Form_Element_Label('pair_number');
        $pairNumberElement->setLabel('Номера пар:');

        $isPerekross_label = new Form_Element_Label('is_perekros_label');
        $isPerekross_label->setLabel('Смена номера:');

        $portSpeedElement = new Form_Element_Label('portspeed');
        $portSpeedElement->setLabel('Скорость:');

        $portStatusElement = new Form_Element_Label('port_status');
        $portStatusElement->setLabel('Состояние порта:');

        $serviceStatusElement = new Form_Element_Label('service_status');
        $serviceStatusElement->setLabel('Состояние услуги:');

        $ballanceStatusElement = new Form_Element_Label('ballance_status');
        $ballanceStatusElement->setLabel('Состояние баланса:');

        $lastSessionDateElement = new Form_Element_Label('last_session_date');
        $lastSessionDateElement->setLabel('Время последней сессии:');

        $tarif_name = new Form_Element_Label('tarif_name');
        $tarif_name->setLabel('Тариф:');

        $letterKindElement = new Zend_Form_Element_Select('letter_kind');
        $letterKindElement->setLabel('Вид письма:')
            ->setAttrib('style', 'width: 200px');

        $letterTypeElement = new Zend_Form_Element_Select('letter_type');
        $letterTypeElement->setLabel('Тип письма:')
            ->setAttrib('style', 'width: 200px');

        $letterSentWayElement = new Zend_Form_Element_Select('letter_sent_way');
        $letterSentWayElement->setLabel('Способ отправки:')
            ->setAttrib('style', 'width: 200px');

        $letterSentDateElement = new Zend_Form_Element_Select('letter_sent_date');
        $letterSentDateElement->setLabel('Дата отправки:')
            ->setAttrib('style', 'width: 200px');

        $formLetterButton = new Zend_Form_Element_Submit('form_letter_button', 'Сформировать');

        $this->addElement($loginElement)
             ->addElement($clientElement)
             ->addElement($lineOwnerElement)
             ->addElement($clientTypeElement)
             ->addElement($serviceTypeElement)
             ->addElement($addressElement)
             ->addElement($dateAgreeElement)
             ->addElement($dateCrossElement)
             ->addElement($managerElement)
             ->addElement($engineerElement)
             ->addElement($phoneElement)
             ->addElement($phoneTypeElement)
             ->addElement($atsElement)
             ->addElement($dslamElement)
             ->addElement($portElement)
             ->addElement($hiddenClientTypeElement)
             ->addElement($frameNumberElement)
             ->addElement($pairNumberElement)
             ->addElement($isPerekross)
             ->addElement($portSpeedElement)
             ->addElement($portStatusElement)
             ->addElement($serviceStatusElement)
             ->addElement($ballanceStatusElement)
             ->addElement($lastSessionDateElement)
             ->addElement($letterKindElement)
             ->addElement($letterTypeElement)
             ->addElement($letterSentWayElement)
             ->addElement($letterSentDateElement)
             ->addElement($faceid)
             ->addElement($country_id)
             ->addElement($formLetterButton)
             ->addElement($tarif_name);
    }

     /**
     * Populate form
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate($values)
    {
        $this->ats_id->addMultiOptions($values['ats_options']);
        $this->dslam_id->addMultiOptions($values['dslam_options']);
        $this->port_id->addMultiOptions($values['port_options']);
        $this->letter_kind->addMultiOptions($values['letter_kind_options']);
        $this->letter_type->addMultiOptions($values['letter_type_options']);
        $this->letter_sent_way->addMultiOptions($values['letter_sent_way_options']);
        $this->letter_sent_date->addMultiOptions($values['letter_sent_date_options']);

		$this->faceid->setValue($values['client_type_id']);
		$this->country_id->setValue($values['country_id']);
        return parent::populate($values);
    }
}
?>
