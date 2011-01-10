<?php
/**
 * Form for summary tarif report
 * 
 * @author tsalik
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_TarifSummaryReport extends Zend_Form 
{	
	public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->addElementPrefixPath('Validate', 'forms/validators', 'VALIDATE');

		$startMonth = new Form_Element_DateSelect('start_month', array('ShowDay' => false));
		$startMonth->setLabel('Начало:')
					->addDecorator('Separator');
		$this->addElement($startMonth);
		
		$finishMonth = new Form_Element_DateSelect('finish_month', array('ShowDay' => false));
		$finishMonth->setLabel('Конец:')
					->addDecorator('Separator')
					->addValidator('DateLaterEqual', false, array('start_month'));
		$this->addElement($finishMonth);
		
        $clientTypeElement = new Zend_Form_Element_Select('client_type');
        $clientTypeElement
            ->addMultiOption('0', 'Корп')
        	->addMultiOption('1', 'Stream')
            ->addMultiOption('2', 'Nova')
        	->setLabel('Тип клиента:')
        	->addDecorator('Separator');
        $this->addElement($clientTypeElement);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}