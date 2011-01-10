<?php
/**
 * Description of ServiceEdit
 *
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');
require_once('forms/elements/Label.php');

class Form_ServiceEdit extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/service/savedates');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $clientElement = new Form_Element_Label('client_name');
        $clientElement->setLabel('Клиент:')
            ->addDecorator('Separator');

        $serviceTypeElement = new Form_Element_Label('servicetype_name');
        $serviceTypeElement->setLabel('Тип услуги:')
            ->addDecorator('Separator');

        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('Начало оказания услуги:')
            ->addDecorator('Separator');

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('Окончание оказания услуги:')
            ->addDecorator('Separator');

        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $submitElement->addDecorator('Separator');

        $tableLinkElement = new Zend_Form_Element_Hidden('tablelink');
        $idElement = new Zend_Form_Element_Hidden('id');
        $pointIDElement = new Zend_Form_Element_Hidden('point_id');
        $clientIDElement = new Zend_Form_Element_Hidden('client_id');
        $idElement->addDecorator('Separator');

        $this->addElement($clientElement)
            ->addElement($serviceTypeElement)
            ->addElement($startDateElement)
            ->addElement($endDateElement)
            ->addElement($submitElement)
            ->addElement($tableLinkElement)
            ->addElement($pointIDElement)
            ->addElement($clientIDElement)
            ->addElement($idElement);
    }
}