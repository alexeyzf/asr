<?php
/**
 * Label.php
 *
 * Label Element for Zend_Form
 *
 * @author marat
 */

require_once('Zend/Form/Element.php');

class Form_Element_Label extends Zend_Form_Element
{
    public $helper = 'formLabel';
}