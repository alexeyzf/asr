<?php
/* 
 * Форма для выбора региона и месяца.
 *
 * @author tsalik
 */

require_once 'Zend/Form.php';
require_once 'Zend/Form/Element/Select.php';
require_once 'forms/elements/DateSelect.php';

class Form_RegionAndMonthSelection extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->addElementPrefixPath('Validate', 'forms/validators', 'VALIDATE');

		$month = new Form_Element_DateSelect('month', array('ShowDay' => false));
		$month->setLabel('Месяц:')
			  ->addDecorator('Separator');
		$this->addElement($month);

        $region = new Zend_Form_Element_Select('region');
        $region->setLabel('Регион:')
			   ->addDecorator('Separator')
               ->addMultiOptions(array(0 => 'Ташкент', 1 => 'Самарканд', 2 => 'Бухара'));
        $this->addElement($region);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}