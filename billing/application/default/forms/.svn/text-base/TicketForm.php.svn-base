<?php
/**
 * Form for add event tickets
 * 
 * @author marat
 */

require_once ('Zend/Form.php');
require_once ('forms/filters/UnixTimeStampFilter.php');

class TicketForm extends Zend_Form 
{
	public function init()
	{
		$this->setMethod('post');
        $this->setAction('#');
        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        
		$organizationCodeElement = new Zend_Form_Element_Select('organization_code');
		$organizationCodeElement->setLabel("Организация")
			->addMultiOption('kvn', 'КВН')
			->setRequired(true)
			->addDecorator('Separator');
		$this->addElement($organizationCodeElement);
		
		$eventDateTimeElement = new Zend_Form_Element_Text('event_time');
		$eventDateTimeElement->setLabel("Дата и время события")
			->addFilter(new UnixTimeStampFilter())
			->addDecorator('Separator');
		$this->addElement($eventDateTimeElement);
		
		$rowsElement = new Zend_Form_Element_Text('row_number');
		$rowsElement->setLabel('Ряд')
			->setRequired(true)
			->addDecorator('Separator');
		$this->addElement($rowsElement);
		
		$placeCountElement = new Zend_Form_Element_Text('place_count');
		$placeCountElement->setLabel('Количество мест')
			->setRequired(true)
			->addDecorator('Separator');
		$this->addElement($placeCountElement);
		
		$priceElement = new Zend_Form_Element_Text('ticket_price');
		$priceElement->setLabel('Цена билета')
			->setRequired(true)
			->addValidator('Digits')
			->addDecorator('Separator');
		$this->addElement($priceElement);
		
		$submtiElement = new Zend_Form_Element_Submit('submit', 'Добавить');
		$submtiElement->addDecorator('Separator');
		$this->addElement($submtiElement);
	}
}