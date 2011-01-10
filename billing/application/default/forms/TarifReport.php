<?php
/**
 * Form for tarif report
 * 
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_TarifReport extends Zend_Form 
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
		
        $clientTypeElement = new Zend_Form_Element_Select('client_type');
        $clientTypeElement->addMultiOption('0', 'Корп')
        	->addMultiOption('1', 'Stream')
        	->setLabel('Тип клиента:')
        	->addDecorator('Separator');
        $this->addElement($clientTypeElement);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}