<?php
/**
 * cover zone modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Coverzone extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $codeElement = new Zend_Form_Element_Text('code');
        $codeElement->setLabel('Код:')
            ->setRequired()
            ->addValidator(new Zend_Validate_Digits());

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement->setLabel('Название:')
            ->setRequired()
            ->addValidator(new Zend_Validate_Alnum());

        $loginPrefixElement = new Zend_Form_Element_Text('prefix');
        $loginPrefixElement->setLabel('Префикс логина:')
            ->setRequired()
            ->addValidator(new Zend_Validate_Alpha());

        $form->addElement($codeElement)
            ->addElement($nameElement)
            ->addElement($loginPrefixElement);
    }
}
?>
