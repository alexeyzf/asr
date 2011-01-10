<?php
/**
 * Decorator for Modem form element
 *
 * @author marat
 */

require_once('Zend/Form/Decorator/Abstract.php');

class Form_Decorator_Modem extends Zend_Form_Decorator_Abstract
{
    private function buildControl()
    {
        $element = $this->getElement();
        $elementName = $element->getName();
        $elementValue = $element->getValue();

        if (is_array($elementValue))
        {
            $modemTypes = $elementValue['modem_types'];

            $modemType = $elementValue['modem_type'];
            $modemSerial = $elementValue['modem_serial'];
            $modemPrice = $elementValue['modem_price'];
            $modemID = $elementValue['modem_id'];
        }

        $modemTypeSelect = $element->getView()->formSelect(
            $elementName . '[modem_type]',
            $modemType,
            array('style' => 'width: 150px;', 'title' => 'Тип модема'),
            $modemTypes
        );

        $modemSerialTextBox = $element->getView()->formText(
            $elementName . '[modem_serial]',
            $modemSerial,
            array('style' => 'width: 100px;', 'title' => 'Серийный номер')
        );

        $modemPriceTextBox = $element->getView()->formText(
            $elementName . '[modem_price]',
            $modemPrice,
            array('style' => 'width: 50px;', 'title' => 'Цена')
        );

        $modemReturnButton = $element->getView()->formButton(
        	$elementName. '_return_modem',
        	"Убрать",
        	array('onclick' => "if (confirm('Вы действительно хотите убрать модем?')) redirect('/service/delete-modem/id/' + $('#{$elementName}-modem_id').val());")
        );

        $modemSaveButton = $element->getView()->formButton(
        	$elementName. '_save_modem',
        	"Сохранить",
        	//array('onclick' => "if (confirm('Вы действительно хотите убрать модем?')) redirect('/service/add-modem/id/' + $('#{$elementName}-modem_id').val());")
        	array('onclick' => "test()")
        );

        $modemIDHidden = $element->getView()->formHidden(
            $elementName . '[modem_id]',
            $modemID
        );

        return $modemTypeSelect . '&nbsp;' . $modemSerialTextBox . '&nbsp;' . $modemPriceTextBox
        	. '&nbsp;' . $modemReturnButton . $modemSaveButton . $modemIDHidden;
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