<?php
require_once('Zend/Form.php');

class Form_Startdate extends Zend_Form
{
    public function init()
    {

		$day   = new Zend_Form_Element_Select('day');
		$month = new Zend_Form_Element_Select('month');
		$year  = new Zend_Form_Element_Select('year');

		$dayend   = new Zend_Form_Element_Select('dayend');
		$monthend = new Zend_Form_Element_Select('monthend');
		$yearend  = new Zend_Form_Element_Select('yearend');


		$this->addElement($day)
			 ->addElement($month)
			 ->addElement($year)
			 ->addElement($dayend)
			 ->addElement($monthend)
			 ->addElement($yearend);

		$day->removeDecorator('label');
		$month->removeDecorator('label');
		$year->removeDecorator('label');
		$dayend->removeDecorator('label');
		$monthend->removeDecorator('label');
		$yearend->removeDecorator('label');

    }

    public function populate($values, $client_type = "")
    {

    	$arrOfMonth = array (
	    		1 => 'Янв',
	    		2 => 'Фев',
	    		3 => 'Мар',
	    		4 => 'Апр',
	    		5 => 'Май',
	    		6 => 'Июн',
	    		7 => 'Июл',
	    		8 => 'Авг',
	    		9 => 'Сен',
	    		10 => 'Окт',
	    		11 => 'Ноя',
	    		12 => 'Дек'
    	);
		$do = 2030;


			$allend = list($yearend, $monthend, $dayend) = explode("-",$values['enddate']);


			$values['dayend']   = $dayend;
			$values['monthend'] = $monthend;
			$values['yearend']  = $yearend;


			$allstart = list($year, $month, $day) = explode("-",$values['startdate']);


			$values['day']   = $day;
			$values['month'] = $month;
			$values['year']  = $year;

			for($d=1; $d<31+1; $d++)
	    	{
	    		if($d < 10)
	    		{
					$this->dayend->addMultiOption("0".$d, $d);
	    		}
	    		else
	    		{
	    			$this->dayend->addMultiOption($d, $d);
	    		}
	    	}

	    	for($m=1; $m<count($arrOfMonth)+1; $m++)
	    	{
	    		if($m < 10)
	    		{
					$this->monthend->addMultiOption("0".$m, $arrOfMonth[$m] );
	    		}
	    		else
	    		{
	    			$this->monthend->addMultiOption($m, $arrOfMonth[$m]);
	    		}
	    	}

	    	for($y=2001; $y<$do+1; $y++)
	    	{
	    		$this->yearend->addMultiOption($y, $y);
	    	}

			for($d=1; $d<31+1; $d++)
	    	{
	    		if($d < 10)
	    		{
					$this->day->addMultiOption("0".$d, $d);
	    		}
	    		else
	    		{
	    			$this->day->addMultiOption($d, $d);
	    		}
	    	}

	    	for($m=1; $m<count($arrOfMonth)+1; $m++)
	    	{
	    		if($m < 10)
	    		{
					$this->month->addMultiOption("0".$m, $arrOfMonth[$m] );
	    		}
	    		else
	    		{
	    			$this->month->addMultiOption($m, $arrOfMonth[$m]);
	    		}
	    	}

	    	for($y=2001; $y<$do+1; $y++)
	    	{
	    		$this->year->addMultiOption($y, $y);
	    	}
		if($client_type == 1)
		{
			$this->removeElement('dayend');
			$this->removeElement('monthend');
			$this->removeElement('yearend');
		}
        return parent::populate($values);
    }
}
?>
