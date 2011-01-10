<?php
 require_once('Zend/Form.php');
 require_once('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');
 require_once('forms/elements/Modem.php');

class Form_conductingForm extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('От')
            ->addDecorator('Separator');
        $this->addElement($startDateElement);

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('До')
            ->addDecorator('Separator');
        $this->addElement($endDateElement);

        //
        $tarif_id = new Zend_Form_Element_Select('tarif_id');
        $tarif_id->setLabel('Тариф: ')
        		->addDecorator('Separator');
        $this->addElement($tarif_id);

        // MODEMS
        $modemElement = new Form_Element_Modem('modem');
        $modemElement->setLabel('Модем:')
                     ->addDecorator('Separator');
        $this->addElement($modemElement);

        // Рег. плата
        $adsl_regpay = new Zend_Form_Element_Text('reg_pay');
        $adsl_regpay->setLabel('Регистрационная плата:')
                    ->addDecorator('Separator');
        $this->addElement($adsl_regpay);

        // Стоимость перелимита
        $traffic_ex = new Zend_Form_Element_Text('traffic_excess');
        $traffic_ex->setLabel('Стоимость 1мб:')
                    ->addDecorator('Separator')
                    ->setAttrib('disabled','disabled')
                    ->setAttrib('size',4);
        $this->addElement($traffic_ex);

        // Стоимость услуги с учетом скидки
        $amount_with_discount = new Zend_Form_Element_Text('amount_with_discount');
        $amount_with_discount->setLabel('Стоимость услуги с уч. скид:')
                    ->addDecorator('Separator')
                    ->setAttrib('disabled','disabled')
                    ->setAttrib('size',4);
        $this->addElement($amount_with_discount);

        $regPayEnable = new Zend_Form_Element_Checkbox('reg_pay_enable');
        $regPayEnable->setLabel('Снять регистрационную плату:')
                     ->addDecorator('Separator');
        $this->addElement($regPayEnable);

        // Рег. плата ($true - то penable = true)
        $penable_status = new Zend_Form_Element_Checkbox('is_forced');
        $penable_status->setLabel('Вступило в силу:')
                       ->addDecorator('Separator');
        $this->addElement($penable_status);

        // Скидка
        $adsl_discount = new Zend_Form_Element_Text('discount');
        $adsl_discount->setLabel('Скидка:  ')
                    ->setAttrib('disabled','disabled');
        $this->addElement($adsl_discount);

        // True скидка
        $check = new Zend_Form_Element_Checkbox('check');
        $check->setAttrib('onchange', 'setDiscount(this.checked)')
            ->addDecorator('Separator');
        $this->addElement($check);

        // коменты к скидке
        $adsl_discountcomment = new Zend_Form_Element_Textarea('discountcomment');
        $adsl_discountcomment->setLabel('Основания для скидки:  ')
                                     ->setAttrib('id', 'some_id')
                                     ->setAttrib('disabled','disabled')
                                     ->addDecorator('Separator');
        $this->addElement($adsl_discountcomment);

        $calcTrafficTypeElement = new Zend_Form_Element_Select('calc_traffic_type');
        $calcTrafficTypeElement->setLabel('Расчет:');
        $calcTrafficTypeElement->addMultiOption('0', 'По входящему траффику');
        $calcTrafficTypeElement->addMultiOption('1', 'По 1/3 от входящего');
        $calcTrafficTypeElement->addMultiOption('2', 'По наибольшему траффику');
        $calcTrafficTypeElement->addDecorator('Separator');
        $this->addElement($calcTrafficTypeElement);

        $isAddApplication = new Zend_Form_Element_Checkbox('is_add_application');
        $isAddApplication->setLabel('Добавить заявку на смнеу IP: ');
        $isAddApplication->addDecorator('Separator');
        $this->addElement($isAddApplication);


        $submit = new Zend_Form_Element_Submit('сохранить');
        $submit->setValue('save');
        $this->addElement($submit);

                // Хиден point_id
        $pnt = new Zend_Form_Element_Hidden('point_id');
                $pnt->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pnt);

        //
        $sid = new Zend_Form_Element_Hidden('servicetype_id');
                $sid->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($sid);

        //
        $client_id = new Zend_Form_Element_Hidden('client_id');
                $client_id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($client_id);


        $need_cross = new Zend_Form_Element_Hidden('need_cross');
                $need_cross->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($need_cross);
        //

        $tablelink = new Zend_Form_Element_Hidden('tablelink');
                $tablelink->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($tablelink);
        //

        $id = new Zend_Form_Element_Hidden('id');
                $id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($id);
        //

        // Хиден CLIENT_TYPE_ID
        $clientTypeID = new Zend_Form_Element_Hidden('client_type_id');
                $clientTypeID->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($clientTypeID);

        $check->removeDecorator('label');
    }

    public function populate($data)
    {

        return parent::populate($data);
    }
}