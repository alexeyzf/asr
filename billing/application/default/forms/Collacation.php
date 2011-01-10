<?php
/*
 * Created on 15.08.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 require_once('Zend/Form.php');
 require_once ('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');
 require_once ('AsrHelp.php');

class Form_Collacation extends Zend_Form
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

        // Тариф
        $tarif_id = new Zend_Form_Element_Select('tarif_id');
        $tarif_id->setLabel('Тариф: ')
        		 ->addDecorator('Separator');
        $this->addElement($tarif_id);

        // Рег. плата
        $adsl_regpay = new Zend_Form_Element_Text('reg_pay');
        $adsl_regpay->setLabel('Регистрационная плата:')
                    ->addDecorator('Separator');
        $this->addElement($adsl_regpay);

        $regPayEnable = new Zend_Form_Element_Checkbox('reg_pay_enable');
        $regPayEnable->setLabel('Снять регистрационную плату:')
                     ->addDecorator('Separator');
        $this->addElement($regPayEnable);

        // Рег. плата ($true - то penable = true)
        $penable_status = new Zend_Form_Element_Checkbox('is_forced');
        $penable_status->setLabel('Вступило в силу:')
                       ->addDecorator('Separator');
        $this->addElement($penable_status);

        // единоразовая оплата
        $one_pay = new Zend_Form_Element_Text('abon_price');
        $one_pay->setLabel('ежемесячно:  ')
        		 ->addDecorator('Separator');
        $this->addElement($one_pay);

        // Стоимость услуги с учетом скидки
        $amount_with_discount = new Zend_Form_Element_Text('amount_with_discount');
        $amount_with_discount->setLabel('Стоимость услуги с уч. скид:')
                    ->addDecorator('Separator')
                    ->setAttrib('disabled','disabled')
                    ->setAttrib('size',4);
        $this->addElement($amount_with_discount);

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
        $adsl_discountcomment->setLabel('Перечень услуг (ежемесячных):  ')
                                     ->setAttrib('id', 'some_id')

                                     ->addDecorator('Separator');
        $this->addElement($adsl_discountcomment);


        // Тип коробки
        //$equipment_type = new Zend_Form_Element_Select('equipment_type');
        //$equipment_type->setLabel('Тип устройства:  ');
        //$this->addElement($equipment_type);

        // кол-unit
        //$unit_count = new Zend_Form_Element_Select('unit_count');
        //$unit_count->setLabel('Количество Unit: ');
        //$this->addElement($unit_count);

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

        $oldTarifID = new Zend_Form_Element_Hidden('old_tarif_id');
        $oldTarifID->setDisableLoadDefaultDecorators(true)
                   ->addDecorator('ViewHelper');
        $this->addElement($oldTarifID);

        $check->removeDecorator('label');
    }

    public function populate($dataOld)
    {
        $equipmentModel = new AsrHelp();
        $dataOld['asrtypes'] = $equipmentModel->getAsrTypeOptions(array('16'));

        if($dataOld['client_type_id'] == 1)
        {
            //$this->removeElement('discountcomment');
            $this->removeElement('discount');
            $this->removeElement('check');
            
            $start = date('Y-m-01', strtotime('+ 1 month'));
            $end = date('Y-m-01', strtotime('+ 2 months'));
            $dataOld['startdate'] = $start;
            $dataOld['enddate']   = $end;
        }

        if($dataOld['id'])
        {
            $this->removeElement('reg_pay');
            $this->removeElement('reg_pay_enable');
            //$this->removeElement('discountcomment');

            $this->removeElement('check');
            $this->removeElement('discount');
        }

        $list = $dataOld['list_service'];

        foreach ($list as $key => $value)
        {
            $this->tarif_id->addMultiOption($value['tarif_id'], $value['tarif_name']);
        }

        $tarifPriceWithDiscount = $dataOld['tarif_price'] - ( $dataOld['tarif_price'] * $dataOld['discount'] / 100);
        $this->amount_with_discount->setValue($tarifPriceWithDiscount);
        
        $this->old_tarif_id->setValue($dataOld['tarif_id']);

        return parent::populate($dataOld);
    }
}

?>
