<?
/**
 * Form for edit phone tarif
 * 
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');
 
class Form_PhoneTarif extends Zend_Form
{
    public function init()
    {
    	$this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
    	
        $this->setMethod('post');
        $this->setAction('#');

        $directionElement = new Zend_Form_Element_Text('directions');
        $directionElement->setLabel('Наименование направления:')
        	->setRequired(true)
        	->addDecorator('Separator')
        	->addValidator(new Zend_Validate_Alnum(true));
        $this->addElement($directionElement);
        
        $utNameElement = new Zend_Form_Element_Text('ut_name');
        $utNameElement->setLabel('Регион:')
        	->setRequired(true)
        	->addDecorator('Separator')
        	->addValidator(new Zend_Validate_Alnum(true));
        $this->addElement($utNameElement);
        
        $prefixElement = new Zend_Form_Element_Text('prefix');
        $prefixElement->setLabel('Префикс:')
        	->setRequired(true)
        	->addDecorator('Separator')
        	->addValidator(new Zend_Validate_Digits());
        $this->addElement($prefixElement);
        
        $priceElement = new Zend_Form_Element_Text('price');
        $priceElement->setLabel('Цена')
        	->setRequired(true)
        	->addDecorator('Separator')
        	->addValidator(new Zend_Validate_Float());
        $this->addElement($priceElement);
        
        $startDateElement = new Form_Element_DateSelect('start_date');
        $startDateElement->setLabel('Дата начала')
        	->addDecorator('Separator');
        $this->addElement($startDateElement);
        
        $endDateElement = new Form_Element_DateSelect('end_date');
        $endDateElement->setLabel('Дата окончания')
        	->addDecorator('Separator');
        $this->addElement($endDateElement);
        
        $saveElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $saveElement->addDecorator('Separator');
        $this->addElement($saveElement);
        
        $idElement = new Zend_Form_Element_Hidden('id');
        $this->addElement($idElement);
    }
}