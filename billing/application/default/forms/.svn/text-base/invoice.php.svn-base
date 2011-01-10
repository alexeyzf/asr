<?php
require_once('Zend/Form.php');


class Form_Invoice extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');
        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$month_need = new Zend_Form_Element_Select('month_need');
        $month_need->setLabel('Счет фактура за: ');
        $month_need->addDecorator('Separator');
        $this->addElement($month_need);

        $year_need = new Zend_Form_Element_Select('year_need');
        $year_need->addDecorator('Separator');
        $this->addElement($year_need);

        $savebtn = new Zend_Form_Element_Submit('сохранить');
        $this->addElement($savebtn);

		$month_need->removeDecorator('lable');
		$month_need->removeDecorator('DtDdWrapper');
		//DtDdWrapper
		$year_need->removeDecorator('lable');
		$year_need->removeDecorator('DtDdWrapper');

    }

    public function populate()
    {
    	$arrMonth = array(
    	 	'01' => 'январь',
    	 	'02' => 'февраль',
    	 	'03' => 'март',
    	 	'04' => 'апрель',
    	 	'05' => 'май',
    	 	'06' => 'июнь',
    	 	'07' => 'июль',
    	 	'08' => 'август',
    	 	'09' => 'сентябрь',
    	 	'10' => 'октябрь',
    	 	'11' => 'ноябрь',
    	 	'12' => 'декабрь'
    	);

		for($y = 2004; $y <= date('Y'); $y++)
		{
			$this->year_need->addMultiOption($y, $y);
		}


        foreach ($arrMonth as $key => $value)
        {
            $this->month_need->addMultiOption($key, $value);
        }

        return parent::populate($arrMonth);
    }
}