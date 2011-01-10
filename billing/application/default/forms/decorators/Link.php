<?php
/**
 * Decorator for link form element
 *
 * @author marat
 */

require_once('Zend/Form/Decorator/Abstract.php');

class Form_Decorator_Link extends Zend_Form_Decorator_Abstract
{
	private function buildControl()
	{
		$element = $this->getElement();
		$onclick = $element->getAttrib('link_onclick');
		$label = $element->getAttrib('link_label');
		$href = $element->getAttrib('link_href');
		$id = $element->getAttrib('link_id');
		$disabled = $element->getAttrib('link_disabled');
		
		if ($disabled)
		{
			$disabled = 'style="display: none"';
		}
		
		return "<a href='{$href}' {$disabled} id={$id} onclick=\"{$onclick}\">{$label}</a>";
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
