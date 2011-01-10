<?php
/**
 * Tech ats modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Techats extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement
            ->setRequired()
            ->addValidator(new Zend_Validate_StringLength(3, 300))
            ->setLabel('АТС:');

        $cityElement = new Zend_Form_Element_Select('city_id');
        $cityElement
            ->setRequired()
            ->setAttrib('style', 'width: 200px')
            ->setLabel('Город включения:');

        $phoneHubElement = new Zend_Form_Element_Select('phone_hub_id');
        $phoneHubElement
            ->setRequired()
            ->setAttrib('style', 'width: 200px')
            ->setLabel('Телефонный узел:');

        $addressElement = new Zend_Form_Element_Text('address');
        $addressElement
            ->setRequired()
            ->addValidator(new Zend_Validate_StringLength(3, 300))
            ->setLabel('Адрес АТС:');

        $statusElement = new Zend_Form_Element_Checkbox('status');
        $statusElement->setLabel('Статус АТС:');

        $notesElement = new Zend_Form_Element_Text('notes');
        $notesElement->setLabel('Примечание:');

        $this->addElement($nameElement)
            ->addElement($cityElement)
            ->addElement($phoneHubElement)
            ->addElement($addressElement)
            ->addElement($statusElement)
            ->addElement($notesElement);
    }

    /**
     * Populate form
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate($values)
    {
        $this->city_id->addMultiOptions($values['city_options']);
        $this->phone_hub_id->addMultiOptions($values['phone_hub_options']);
        unset($values['city_options']);
        unset($values['phone_hub_options']);

        return parent::populate($values);
    }
}