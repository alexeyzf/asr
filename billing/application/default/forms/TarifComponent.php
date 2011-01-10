<?php
require_once('Zend/Form.php');

class Form_TarifComponent extends Zend_Form
{
	public function init()
	{
		$component_name = new Zend_Form_Element_Text('component_name');
		$component_name->setRequired(true)
					   ->setAttrib('id', 'some_id');
		$component_name->removeDecorator('label')
		      		   ->removeDecorator('DtDdWrapper');
		$this->addElement($component_name);

		$weekday = new Zend_Form_Element_Select('weekday');
		$this->addElement($weekday);
		$weekday->removeDecorator('label')
		      		   ->removeDecorator('DtDdWrapper');

		// Время нач.
		$hours = new Zend_Form_Element_Select('hours');
		$this->addElement($hours);
		$hours->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

		// Минута нач.
		$minut = new Zend_Form_Element_Select('minut');
		$this->addElement($minut);
		$minut->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

		// Сек. нач.
		$sec = new Zend_Form_Element_Select('sec');
		$this->addElement($sec);
		$sec->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

//////////////////
		// Время end.
		$hoursend = new Zend_Form_Element_Select('hoursend');
		$this->addElement($hoursend);
		$hoursend->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

		// Минута end.
		$minutend = new Zend_Form_Element_Select('minutend');
		$this->addElement($minutend);
		$minutend->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

		// Сек. end.
		$secend = new Zend_Form_Element_Select('secend');
		$this->addElement($secend);
		$secend->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');

		$traffic_excess = new Zend_Form_Element_Text('traffic_excess');
		$traffic_excess->setRequired(true);
		$traffic_excess->removeDecorator('label')
		      ->removeDecorator('DtDdWrapper');
		$this->addElement($traffic_excess);

	}

	public function populate($values)
    {
    	//$asrTypes = $values['asrtypes'];

    	$arrWeeks = array (
    		0 => 'Все дни',
    		1 => 'Рабочие дни',
    		2 => 'Выходные дни'
    	);

    	foreach ($arrWeeks as $key => $value)
    	{
    		$this->weekday->addMultiOption($key, $value);
    	}

    	for($h=0; $h<23+1; $h++ )
    	{
    		if($h < 10)
    		{
				$this->hours->addMultiOption("0".$h, "0".$h);
				$this->hoursend->addMultiOption("0".$h, "0".$h);
    		}
    		else
    		{
    			$this->hours->addMultiOption($h, $h);
				$this->hoursend->addMultiOption($h, $h);
    		}

    	}

    	for($mc=0; $mc<59+1; $mc++ )
    	{
    		if($mc < 10)
    		{
				$this->minut->addMultiOption("0".$mc, "0".$mc);
				$this->minutend->addMultiOption("0".$mc, "0".$mc);

				$this->sec->addMultiOption("0".$mc, "0".$mc);
				$this->secend->addMultiOption("0".$mc, "0".$mc);
    		}
    		else
    		{
    			$this->minut->addMultiOption($mc, $mc);
				$this->minutend->addMultiOption($mc, $mc);

				$this->sec->addMultiOption($mc, $mc);
				$this->secend->addMultiOption($mc, $mc);
    		}
    	}


        return parent::populate($values);
    }
}


?>
