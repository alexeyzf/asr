<?php
/**
 * Form for pintel service
 * 
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Pintel extends Zend_Form 
{
	public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        
        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('От')
            ->addDecorator('Separator');
        $this->addElement($startDateElement);

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('До')
            ->addDecorator('Separator');
        $this->addElement($endDateElement);
        
        $loginElement = new Zend_Form_Element_Text('u_login');
        $loginElement->setLabel('Логин:')
        	->addDecorator('Separator')
        	->setValue('pin_');
        $this->addElement($loginElement);
        
        $passwordElement = new Zend_Form_Element_Password('u_passwd');
        $passwordElement->setLabel('Пароль:')
        	->addDecorator('Separator');
        $this->addElement($passwordElement);
        
         // Рег. плата ($true - то penable = true)
        $penableElement = new Zend_Form_Element_Checkbox('is_forced');
        $penableElement->setLabel('Вступило в силу:')
                       ->addDecorator('Separator');
        $this->addElement($penableElement);
        
        $submit = new Zend_Form_Element_Submit('сохранить');
        $submit->setValue('save');
        $this->addElement($submit);
        
        $pointElement = new Zend_Form_Element_Hidden('point_id');
        $pointElement->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pointElement);
        
        $tableLinkElement = new Zend_Form_Element_Hidden('tablelink');
        $tableLinkElement->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($tableLinkElement);
       
        $serviceIDElement = new Zend_Form_Element_Hidden('id');
        $serviceIDElement->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($serviceIDElement);
        
        $serviceTypeIDElement = new Zend_Form_Element_Hidden('servicetype_id');
        $serviceTypeIDElement->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($serviceTypeIDElement);
        
        $clientIDElement = new Zend_Form_Element_Hidden('client_id');
        $clientIDElement->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($clientIDElement);
    }
    
    public function populate($data)
    {
    	$data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
    	$data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
    	return parent::populate($data);
    }
}