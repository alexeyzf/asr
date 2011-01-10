<?php
/**
 * Decorator for date select form element
 *
 * @author marat
 */

require_once('Zend/Form/Decorator/Abstract.php');
require_once('Zend/Date.php');

class Form_Decorator_DateSelect extends Zend_Form_Decorator_Abstract
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

    private function getMonthOptions()
    {
        return array(
            '01'    => 'Янв',
            '02'    => 'Фев',
            '03'    => 'Мар',
            '04'    => 'Апр',
            '05'    => 'Май',
            '06'    => 'Июн',
            '07'    => 'Июл',
            '08'    => 'Авг',
            '09'    => 'Сен',
            '10'    => 'Окт',
            '11'    => 'Ноя',
            '12'    => 'Дек'
        );
    }

    private function buildControl()
    {
        $element = $this->getElement();
        $elementName = $element->getName();
        $elementValue = $element->getValue();
        
        if (!$this->getOption('showDay') & $elementValue != NULL) {
        	$elementValue .= '-01'; // дебильный изварот
        }
        
        $date = new Zend_Date($elementValue);
        $day = $date->get(Zend_Date::DAY);
        $month = $date->get(Zend_Date::MONTH);
        $year = $date->get(Zend_Date::YEAR);

        $dayOptions = $this->getDateOptions(1, 31);
        $monthOptions = $this->getMonthOptions();
        $yearOptions = $this->getDateOptions(1990, 2020);

        if ($this->getOption('showDay')) {
	        $daySelect = $element->getView()->formSelect(
	            $elementName . '_day',
	            $day,
	            array('style' => 'width: 50px;'),
	            $dayOptions
	        );
        }

        $monthSelect = $element->getView()->formSelect(
            $elementName . '_month',
            $month,
            $element->getAttribs(),
            $monthOptions
        );

        $yearSelect = $element->getView()->formSelect(
            $elementName . '_year',
            $year,
            $element->getAttribs(),
            $yearOptions
        );

        if ($this->getOption('showDay')) 
        {
        	return $daySelect . '&nbsp;' . $monthSelect . '&nbsp;' . $yearSelect;
        }
        else 
        {
        	return $monthSelect . '&nbsp;' . $yearSelect;
        }
    }

    public function render($content)
    {
        $element = $this->getElement();

        if (!$element instanceof Zend_Form_Element)
        {
            return $content;
        }

        if (null === $element->getView())
        {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();

        $output = $this->buildControl();

        switch ($placement)
        {
            case (self::PREPEND):
                return $output . $separator . $content;

            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }
}