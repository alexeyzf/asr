<?php
/*
 * Created on 15.08.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 require_once('Zend/Form.php');
 require_once('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');
 require_once('forms/elements/Modem.php');

class Form_Adsl extends Zend_Form
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

		// Скорость дин анлима
        $tarif_speed_unlim = new Zend_Form_Element_Text('tarif_speed_unlim');
        $tarif_speed_unlim->setLabel('Скорость: ')
        		->setAttrib('onchange', 'return setPrice()')
        		->addDecorator('Separator')
        		->setAttrib('id', 'dynamic_speed');
        $this->addElement($tarif_speed_unlim);

		// Цена дин анлима
        $tarif_price_unlim = new Zend_Form_Element_Text('tarif_price_unlim');
        $tarif_price_unlim->setLabel('Цена: ')
        		->addDecorator('Separator')
        		->setAttrib('readonly','readonly')
        		->setAttrib('id', 'dynamic_price');
        $this->addElement($tarif_price_unlim);

		// UP speed дин анлима
        $tarif_speed_up_unlim = new Zend_Form_Element_Text('tarif_speed_up_unlim');
        $tarif_speed_up_unlim->setLabel('Исх. скорость: ')
        		->addDecorator('Separator')
        		->setAttrib('readonly','readonly')
        		->setAttrib('id', 'speedup_unlim');
        $this->addElement($tarif_speed_up_unlim);

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

		// Скорость
        $speed = new Zend_Form_Element_Text('speed');
        $speed->setLabel('Скорость:')
                    ->addDecorator('Separator')
                    ->setAttrib('disabled','disabled');
        $this->addElement($speed);

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

    public function populate($dataOld)
    {
		//var_dump($dataOld);
		//exit();
        $tarifPriceWithDiscount = $dataOld['tarif_price'] - ( $dataOld['tarif_price'] * $dataOld['discount'] / 100);


        if($dataOld['client_type_id'] == 1)
        {
            $this->removeElement('discountcomment');
            $this->removeElement('discount');
            $this->removeElement('check');
            $this->removeElement('is_forced');
            $this->removeElement('calc_traffic_type');

            if ($dataOld['id'])
            {
            	$this->removeElement('startdate');
            	$this->removeElement('enddate');
            }

            $start = date('Y-m-d', strtotime($dataOld['startdate']));
            $end = date('Y-m-d', strtotime($dataOld['enddate']));
            $dataOld['startdate'] = $start;
            $dataOld['enddate']   = $end;

            $this->traffic_excess->setValue($dataOld['tarif_components'][0]['traffic_excess']);
        }

        if($dataOld['id'])
        {
            //$this->removeElement('modem');
            $this->removeElement('reg_pay');
            $this->removeElement('reg_pay_enable');
        }
        else
        {
        	$this->removeElement('is_add_application');
        }

        $list = $dataOld['list_service'];

        foreach ($list as $key => $value)
        {
            $this->tarif_id->addMultiOption($value['tarif_id'], $value['tarif_name']);
        }

        $this->amount_with_discount->setValue($tarifPriceWithDiscount);
        $this->traffic_excess->setValue($dataOld['tarif_components'][0]['traffic_excess']);
        return parent::populate($dataOld);
    }
}