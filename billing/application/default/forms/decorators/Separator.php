<?php
/**
 * Decorator for separator
 *
 * @author marat
 */

require_once('Zend/Form/Decorator/Abstract.php');

class Form_Decorator_Separator extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        return $content . '<div class="clear"></div>';
    }
}