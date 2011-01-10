<?php
/**
 * Admin group modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Admingroup extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement
            ->setRequired()
            ->addValidator(new Zend_Validate_Alnum(true))
            ->addValidator(new Zend_Validate_StringLength(3, 300))
            ->setLabel('Название:');

        $this->addElement($nameElement);
    }
}