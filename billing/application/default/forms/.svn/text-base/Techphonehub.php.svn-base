<?php
/**
 * Tech phone hub modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Techphonehub extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement->setLabel('Название:')
            ->setRequired()
            ->addValidator(new Zend_Validate_Alnum(true));

        $addressElement = new Zend_Form_Element_Text('address');
        $addressElement->setLabel('Адрес:')
            ->setRequired();

        $directorElement = new Zend_Form_Element_Text('director');
        $directorElement->setLabel('Директор:')
            ->setRequired();

        $this->addElement($nameElement)
            ->addElement($addressElement)
            ->addElement($directorElement);
    }
}
?>
