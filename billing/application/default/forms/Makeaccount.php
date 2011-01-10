<?php
/*
 * Created on 15.08.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once ('Zend/Form.php');
require_once ('forms/Startdate.php');
require_once ('forms/elements/Label.php');
require_once ('forms/elements/DateSelect.php');
require_once ('Asr/FormHelper.php');

class Form_Makeaccount extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');
		$this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
		
		$clientLabel = new Form_Element_Label('client_name');
		$clientLabel->setLabel('Клиент:')
			->addDecorator('Separator');
		$this->addElement($clientLabel);
		
		//Тип документа
		$documentTypeElement = new Zend_Form_Element_Select('document_type');
		$documentTypeElement->setLabel('Тип:')
			->addDecorator('Separator')
			->addMultiOption(1, 'Счет')
			->addMultiOption(2, 'Счет фактура')
			->addMultiOption(3, 'Акт сверки');
		$this->addElement($documentTypeElement);
		
		$withoutPrepayElement = new Zend_Form_Element_Checkbox('without_prepay');
		$withoutPrepayElement->setLabel('Без предоплаты:')
							->addDecorator('Separator');
		$this->addElement($withoutPrepayElement);
		
		$startMonthElement = new Zend_Form_Element_Select('start_month');
		
		$arrOfMonth = array (
    		'01' => 'Янв',
    		'02' => 'Фев',
    		'03' => 'Мар',
    		'04' => 'Апр',
    		'05' => 'Май',
    		'06' => 'Июн',
    		'07' => 'Июл',
    		'08' => 'Авг',
    		'09' => 'Сен',
    		'10' => 'Окт',
    		'11' => 'Ноя',
    		'12' => 'Дек'
    	);
    	
		$startMonthElement->addMultiOptions($arrOfMonth);
		$startMonthElement->setLabel('Месяц:')
			->addDecorator('Separator');
		$this->addElement($startMonthElement);
		
		$startYearElement = new Zend_Form_Element_Select('start_year');
    	
		for ($i = 2004; $i <= intval(date('Y')) + 1; $i++)
    	{
    		$arrOfYears[$i] = $i;
    	}
    	
		$startYearElement->addMultiOptions($arrOfYears);
		$startYearElement->setLabel('Год:')
			->addDecorator('Separator');
		$this->addElement($startYearElement);

		// Кнопка
        $submit = new Zend_Form_Element_Submit('Сформировать');
        $submit->setValue('save');
        $this->addElement($submit);

        // ID клиента
        $client_id = new Zend_Form_Element_Hidden('client_id');
                $client_id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($client_id);
    }
}