<?
require_once('Zend/Form/Decorator/Abstract.php');

class Form_Decorator_IsdnNumbers extends Zend_Form_Decorator_Abstract
{
    private function getNumberElement($view, $elementName, $numberID, $number)
    {
        return $view->formText(
            "{$elementName}[{$numberID}][number]",
            $number,
            array(
                'style'    => 'width: 150px;',
                'title'    => 'Номер'
            )
        );
    }

    private function getPriceElement($view, $elementName, $numberID, $numberPrice)
    {
        return $view->formText(
            "{$elementName}[{$numberID}][directline_number]",
            $numberPrice,
            array(
                'style'    => 'width: 150px;',
                'title'    => 'Цена'
            )
        );
    }

    private function getImg($view, $numberID)
    {
        return "<img src='/images/icons/delete.png' title='Удалить' onclick=\"removeWithConfirm('numbers{$numberID}')\" />";
    }

    private function getSpan($numberID, $content)
    {
        return "<span id='numbers{$numberID}'>{$content}<br/></span>";
    }

    private function buildControl()
    {
        $element = $this->getElement();
        $elementName = $element->getName();
        $elementValue = $element->getValue();
        $view = $element->getView();

        $result = "<div id='numbers'>";

        if ( is_array($elementValue['numbers']) )
        {
            foreach ($elementValue['numbers'] as $number)
            {
                $numberElement = $this->getNumberElement($view, $elementName, $number['id'], $number['number']) ;
                $priceElement = $this->getPriceElement($view, $elementName, $number['id'], $number['directline_number']);
                $imgElement = $this->getImg($view, $number['id']);

                $result .= $this->getSpan($number['id'], "{$numberElement}&nbsp;{$typeElement}&nbsp;{$priceElement}&nbsp;{$imgElement}");
            }
        }

        $result .= "</div>";

        $result .= "<div id='numbers_template' class='hide'>";
        $numberElement = $this->getNumberElement($view, $elementName, 'newCOUNTER', NULL) ;
        $priceElement = $this->getPriceElement($view, $elementName, 'newCOUNTER', NULL);
        $imgElement = $this->getImg($view, 'newCOUNTER');
        $result .= $this->getSpan('newCOUNTER', "{$numberElement}&nbsp;{$typeElement}&nbsp;{$priceElement}&nbsp;{$imgElement}");
        $result .= "</div>";

        if ( count($elementValue['numbers']) == 0 )
        {
            $result .= "
                <script>
                	addDiv('numbers', 'numbers_template');
                </script>
            ";
        }

        $result .= '<a href="#numbers" onclick="addDiv(\'numbers\', \'numbers_template\')">Добавить</a>';

        return $result;
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
?>