<?
/**
 * Form for showing date period control
 * 
 * @author tsalik
 */
 
require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_MonthDatePeriod extends Zend_Form
{
	private function getDateOptions($start, $end)
    {
        for ($i = $start; $i <= $end; $i++)
        {
            if ($i < 10)
            {
                $key = "0{$i}";
            }
            else
            {
                $key = $i;
            }

            $options[$key] = $i;
        }

        return $options;
    }

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

        $dayNumber = new Zend_Form_Element_Select('day_number');
        $dayNumber->setLabel('День:')
                  ->addDecorator('Separator')
                  ->addMultiOptions($this->getDateOptions(1, 31));
        $this->addElement($dayNumber);

        $submit = new Zend_Form_Element_Submit('Показать');
        $submit->setValue('show');
        $this->addElement($submit);
    }
}