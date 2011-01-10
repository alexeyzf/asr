<?php
/**
 * АТС Бонус форма для добавления
 * @author marat
 *
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');
 
class AtsBonusForm extends Zend_Form 
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction('#');
        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        
		$pointSelectElement = new Zend_Form_Element_Select('point_id');
		$pointSelectElement->setLabel('Точка')
            ->addDecorator('Separator')
            ->setRequired();
        $this->addElement($pointSelectElement);
        
        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('От')
            ->addDecorator('Separator')
            ->setRequired();
        $this->addElement($startDateElement);

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('До')
            ->addDecorator('Separator')
            ->setRequired();
        $this->addElement($endDateElement);
        
        $commentElement = new Zend_Form_Element_Text('notes');
        $commentElement->setLabel('Причина')
        	->addDecorator('Separator')
        	->setRequired();
        $this->addElement($commentElement);
        
        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $submitElement->addDecorator('Separator');
        $this->addElement($submitElement);
	}
	
	public function populate($data)
    {
    	foreach ($data['points'] as $point)
    	{
    		$this->point_id->addMultiOption($point['point_id'], $point['u_login']);
    	}
    	
    	parent::populate($data);
    }
    
    public function isValid($data)
    {
    	foreach ($data['points'] as $point)
    	{
    		$this->point_id->addMultiOption($point['point_id'], $point['u_login']);
    	}
    	
    	return parent::isValid($data);
    }
}