<?php
/**
 * Form for create card order page
 *
 * @author marat
 */

require_once ('Zend/Form.php');
require_once ('forms/elements/Label.php');
require_once ('forms/elements/DateSelect.php');

class Form_CardOrder extends Zend_Form
{
    public function init()
    {
        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $nameElement = new Form_Element_Label('client');
        $nameElement->setLabel('Наименование:')
            ->addDecorator('Separator');
        $this->addElement($nameElement);

        $numberElement = new Zend_Form_Element_Text('number');
        $numberElement->setLabel('Номер заказа:')
            ->setRequired()
            ->addDecorator('Separator');
        $this->addElement($numberElement);

        $invoiceNumberElement = new Zend_Form_Element_Text('invoice_number');
        $invoiceNumberElement->setLabel('Номер накладной:')
            ->setRequired()
            ->addDecorator('Separator');
        $this->addElement($invoiceNumberElement);

        $orderDateElement = new Form_Element_DateSelect('order_date');
        $orderDateElement->setLabel('Дата заказа:')
            ->setRequired()
            ->addDecorator('Separator');
        $this->addElement($orderDateElement);

        $cardTypeElement = new Zend_Form_Element_Select('card_type');
        $cardTypeElement->setLabel('Тип карт:')
            ->setRequired()
            ->addDecorator('Separator')
            ->addMultiOption(1, 'Sigma')
            ->addMultiOption(2, 'Smile');
        $this->addElement($cardTypeElement);

        $unit3Element = new Zend_Form_Element_Text('details3');
        $unit3Element->setLabel('3 единицы:')
            ->addDecorator('Separator');
        $this->addElement($unit3Element);

        $unit5Element = new Zend_Form_Element_Text('details5');
        $unit5Element->setLabel('5 единиц:')
            ->addDecorator('Separator');
        $this->addElement($unit5Element);

        $unit10Element = new Zend_Form_Element_Text('details10');
        $unit10Element->setLabel('10 единиц:')
            ->addDecorator('Separator');
        $this->addElement($unit10Element);

        $unit15Element = new Zend_Form_Element_Text('details15');
        $unit15Element->setLabel('15 единиц:')
            ->addDecorator('Separator');
        $this->addElement($unit15Element);

        $unit20Element = new Zend_Form_Element_Text('details20');
        $unit20Element->setLabel('20 единиц:')
            ->addDecorator('Separator');
        $this->addElement($unit20Element);

        $clientIDElement = new Zend_Form_Element_Hidden('client_id');
        $clientIDElement->addDecorator('Separator');
        $this->addElement($clientIDElement);

        $saveElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $this->addElement($saveElement);
    }

    public function populate($data)
    {
        $this->invoice_number->setValue($data['max_invoice']);
        $this->number->setValue($data['next_order_number']);
        $this->client->setValue($data['client']);
        $this->client_id->setValue($data['client_id']);
    }
}