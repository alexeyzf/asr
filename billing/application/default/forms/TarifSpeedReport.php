<?php
/**
 * Form for tarif speed report
 * 
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_TarifSpeedReport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$dateElement = new Form_Element_DateSelect('date');
		$dateElement->setLabel('Дата:')
					->addDecorator('Separator');
		$this->addElement($dateElement);
		
        $service = new Zend_Form_Element_Select('service');
        $service->setLabel('Услуга:')
        		->addDecorator('Separator')
        		->addMultiOption('(3000, 7000, 7020)', 'Интернет')
        		->addMultiOption('(8000)', 'VPN')
        		->addMultiOption('(3100, 7100, 7110)', 'Localnet');
        $this->addElement($service);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}