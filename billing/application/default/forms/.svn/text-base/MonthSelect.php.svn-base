<?
/**
 * Form for showing date period control
 *
 * @author tsalik
 */

require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_MonthSelect extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->addElementPrefixPath('Validate', 'forms/validators', 'VALIDATE');

		$startMonth = new Form_Element_DateSelect('start_month', array('ShowDay' => false));
		$startMonth->setLabel('Месяц:')
					->addDecorator('Separator');
		$this->addElement($startMonth);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}