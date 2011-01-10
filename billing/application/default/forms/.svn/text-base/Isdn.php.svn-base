<?php
require_once('Zend/Form.php');

require_once('forms/elements/IsdnNumbers.php');

class Form_Isdn extends Zend_Form
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

        // выбор тарийа
        $tarifIDElement = new Zend_Form_Element_Select('tarif_id');
        $tarifIDElement->setLabel('Тариф:');
        $this->addElement($tarifIDElement);

        // рег. плата
        $regpayElement = new Zend_Form_Element_Text('reg_pay');
        $regpayElement->setLabel('Регистрационная плата:')
                      ->addDecorator('Separator');
        $this->addElement($regpayElement);

        $regPayEnable = new Zend_Form_Element_Checkbox('reg_pay_enable');
        $regPayEnable->setLabel('Снять регистрационную плату:')
                     ->addDecorator('Separator');
        $this->addElement($regPayEnable);

        // Есть ли рег. плата ($true - то penable = true)
        $penableElement = new Zend_Form_Element_Checkbox('is_forced');
        $penableElement->setLabel('Вступило в силу:')
                    ->addDecorator('Separator');
        $this->addElement($penableElement);

        // единоразовая оплата
        $one_pay = new Zend_Form_Element_Text('abon_price');
        $one_pay->setLabel('ежемесячно:  ')
        		 ->addDecorator('Separator');
        $this->addElement($one_pay);

        // Скидка
        $discountElement = new Zend_Form_Element_Text('discount');
        $discountElement->setLabel('Скидка:')
                    ->setAttrib('disabled','disabled');
        $this->addElement($discountElement);

        // Есть ли скидка
        $discountCheckElement = new Zend_Form_Element_Checkbox('check');
        $discountCheckElement ->setAttrib('onchange', 'setDiscount(this.checked)')
            ->addDecorator('Separator')
            ->removeDecorator('label');
        $this->addElement($discountCheckElement );

        // Коментарии к скидке
        $discountCommentElement = new Zend_Form_Element_Textarea('discountcomment');
        $discountCommentElement->setLabel('Основания для скидки:  ')
                                     ->setAttrib('id', 'some_id')
                                     ->setAttrib('disabled','disabled')
                                     ->addDecorator('Separator');
        $this->addElement($discountCommentElement);

        //доступ к МГ и МН связи
        $foreignAccessElement = new Zend_Form_Element_Checkbox('foreign_access');
        $foreignAccessElement->setLabel('Доступ к МГ и МН свзязи:')
            ->addDecorator('Separator');
        $this->addElement($foreignAccessElement);

        //NGN номера
        $ngnNumbersElement = new Form_Element_IsdnNumbers('numbers');
        $ngnNumbersElement->setLabel('Номера:')
            ->addDecorator('Separator');
        $this->addElement($ngnNumbersElement);

        $submit = new Zend_Form_Element_Submit('save', 'сохранить');
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
    }

    public function populate($data)
    {
        if($data['client_type_id'] == 1)
        {
            $this->removeElement('discountcomment');
            $this->removeElement('discount');
            $this->removeElement('check');

            $start = date('Y-m-01', strtotime('+ 1 month'));
            $end = date('Y-m-01', strtotime('+ 2 months'));
            $data['startdate'] = $start;
            $data['enddate']   = $end;
        }

        if($data['id'])
        {
            $this->removeElement('reg_pay');
            $this->removeElement('reg_pay_enable');
            $this->removeElement('discountcomment');
            $this->removeElement('check');
            $this->removeElement('discount');
        }

        $list = $data['list_service'];

        foreach ($list as $key => $value)
        {
            $this->tarif_id->addMultiOption($value['tarif_id'], $value['tarif_name']);
        }

        $this->old_tarif_id->setValue($data['tarif_id']);

        return parent::populate($data);
    }
}
?>