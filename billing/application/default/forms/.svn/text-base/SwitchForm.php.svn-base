<?php
/**
 * Edit form for switch
 * 
 * @author marat
 */

require_once ('Zend/Form.php');

class SwitchForm extends Zend_Form 
{
	public function init()
	{
		$this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        
        $ipAddressElement = new Zend_Form_Element_Text('ip_address');
        $ipAddressElement->setLabel('IP адрес:')
        	->addValidator(new Zend_Validate_Ip())
        	->addDecorator('Separator');
        $this->addElement($ipAddressElement);
        
        $atsElement = new Zend_Form_Element_Select('ats_id');
        $atsElement->setLabel('Узел:')
        	->addDecorator('Separator')
        	->addMultiOption('', '')
        	->addFilter(new Zend_Filter_Int());
        $atsModel = new AtsList();
        $atsElement->addMultiOptions($atsModel->getOptions());
        $this->addElement($atsElement);
        
        $typeElement = new Zend_Form_Element_Select('switch_type_id');
        $typeElement->setLabel('Тип:')
        	->setRequired(true)
        	->addDecorator('Link')
        	->addDecorator('Separator')
        	->setAttrib('style', 'width: 200px')
        	->setAttrib('link_label', "Добавить")
        	->setAttrib('link_href', "#")
        	->setAttrib('link_id', 'switch_type_link');
        $swithesTypesModel = new SwitchesTypesModel();
        $typeElement->addMultiOptions($swithesTypesModel->getOptions());
        $this->addElement($typeElement);
        
        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $submitElement->addDecorator('Separator');
        $this->addElement($submitElement);
        
        $IDElement = new Zend_Form_Element_Hidden('id');
        $IDElement->addDecorator('Separator')
        	->addFilter(new Zend_Filter_Int());
        $this->addElement($IDElement);
	}
}
