<?
/**
 * Form for showing date period control
 *
 * @author marat
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_DatePeriod extends Zend_Form
{
	public function init()
 	{
 		$this->setMethod('post');
 		$this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('От')
        	->removeDecorator('Errors')
        	->removeDecorator('Description')
        	->removeDecorator('HtmlTag')
        	->removeDecorator('Label')
        	->addDecorator('Label', array('separator' => ' ', 'class' => 'search'));
        $this->addElement($startDateElement);

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('до')
            ->removeDecorator('Errors')
        	->removeDecorator('Description')
        	->removeDecorator('HtmlTag')
        	->removeDecorator('Label')
        	->addDecorator('Label', array('separator' => ' ', 'class' => 'search'));
        $this->addElement($endDateElement);

        $searchButton = new Zend_Form_Element_Submit('search', 'Показать');
        $searchButton->clearDecorators()
            ->addDecorator('ViewHelper');
        $this->addElement($searchButton);
 	}
}