<?php
/*
 * В данном контроллере будет реализована функция назначения услуг
 * в зависимостиот шаблона Template
 */

require_once ('AdslModel.php');
require_once ('ClientModel.php');
require_once ('TarifListModel.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('CollacationModel.php');
require_once ('Asr/FormHelper.php');

class AddserviceController extends Zend_Controller_Action {

  public function indexAction()
  {

  }

  public function adslAction()
  {
    if ($this->_request->isPost())
    {
    // Принимаем параметры от Хелпера Template.php
      $adsl_settings = $this->_request->getPost();

      // смотрим на выбранный тариф, загоняем значение статуса need_cross
      // в переменную $row_need (fetchOne!!!)
    //$service_needCross = new PointServices();
    //$row_need = $service_needCross->verifyNeedCross($adsl_settings['service_id']);

    // Модель для записи в таблицу adsl
    $adslInsertModel = new AdslModel();
    $row_adsl = $adslInsertModel->createRow(); // создаем пустую строку в таблицу Adsl
    $row_adsl->discountcomment = $adsl_settings['adsl_discountcomment'];
    $row_adsl->reg_pay = $this->calcDiscount($adsl_settings['adsl_regpay'], $adsl_settings['adsl_discount']);
    $row_adsl->tarif_id = $adsl_settings['tarif_uid'];
    $row_adsl->point_id = $adsl_settings['pnt'];
    $row_adsl->service_address = $adsl_settings['service_address'];
    $row_adsl->contact_name = $adsl_settings['contact_name'];
    $row_adsl->contact_phone = $adsl_settings['contact_phone'];

        // Startdate and EndDate
    if($adsl_settings['month_select'] == 12)
    {
            // Если номер месяца у нас 12 то делаем следующий месяц 1
            $m = 1;
    } else {
            $m = $adsl_settings['month_select']+1;
    }
    $d = 1;
    $plusMonth = $adsl_settings['continue_year']. '-'. $m. '-'. $d;


    if($adsl_settings['client_type_id'] == 0)
    {
        $row_adsl->startdate = $adsl_settings['year_select']. '-'. $adsl_settings['month_select']. '-'. $adsl_settings['days'];
        $row_adsl->enddate = $adsl_settings['continue_year']. '-'. $adsl_settings['continue_month']. '-'. $adsl_settings['continue_days'];
        $row_adsl->penable = true;
    } else {
        $row_adsl->startdate = $adsl_settings['year_select']. '-'. $adsl_settings['month_select']. '-'. $adsl_settings['days'];
        $row_adsl->enddate = $adsl_settings['year_select']. '-'. $m. '-'. $d;
        $row_adsl->penable = true;
    }


    $adsl_id = $row_adsl->save();

    // Логирование
    AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_SERVICE_ADSL, $_SERVER['REQUEST_URI'], $adsl_settings['client_id'], $adsl_settings['pnt']);

    $this->_redirect($_SESSION['back_url']); // Если все нормально делаем редерект
    }

  }



  public function collacationAction()
  {
    if ($this->_request->isPost())
    {
        $colla_settings = $this->_request->getPost(); // Принимаем

        $collaInsertModel = new CollacationModel(); // создаем модель для вставки данных из формы для услуги collacation
        $row_colla = $collaInsertModel->createRow();
        $row_colla->colla_regpay          = $this->calcDiscount($colla_settings['colla_regpay'], $colla_settings['colla_discount']);
        $row_colla->colla_discount        = $colla_settings['colla_discount'];
        $row_colla->colla_discountcomment = $colla_settings['colla_discountcomment'];
        $row_colla->point_id              = $colla_settings['pnt'];
        $row_colla->tarif_id              = $colla_settings['tarif_uid'];
        $row_colla->contact_phone         = $colla_settings['contact_phone'];
        $row_colla->contact_name          = $colla_settings['contact_name'];
        $row_colla->connect_address       = $colla_settings['service_address'];
        $row_colla->penable               = 'true';

        // End date для услуги collacation
        $day = 1;
        // Если 12 месяц то делаем след. месяц 1
        if(date("m") == 12)
        {
            $month = 1;
        } else {
            $month = date("m")+1;
        }
        $row_colla->enddate = date("Y").'-'.$month.'-'.$day;
        $colla_id = $row_colla->save();

        // Все прошло успешно, делаем режирект
        // Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_SERVICE_COLLA, $_SERVER['REQUEST_URI'], $colla_settings['client_id'], $colla_settings['pnt']);

        $this->_redirect($_SESSION['back_url']); // Если все нормально делаем редерект
    }
  }


  public function ngnAction()
  {
    if ($this->_request->isPost())
    {
        $ngn_settings = $this->_request->getPost();

        $ngnInsertModel = new NgnModel();
        $row_ngn = $ngnInsertModel->createRow();
        $row_ngn->ngn_regpay      = $ngn_settings['ngn_regpay'];
        $row_ngn->ngn_discount    = $ngn_settings['ngn_discount'];
        $row_ngn->tarif_id        = $ngn_settings['tarif_uid'];
        $row_ngn->point_id        = $ngn_settings['pnt'];
        $row_ngn->service_address = $ngn_settings['service_address'];
        $row_ngn->contact_name    = $ngn_settings['contact_name'];
        $row_ngn->contact_phone   = $ngn_settings['contact_phone'];

        // End insert
        // если все ок то делаем save
        $ngn_id = $row_ngn->save();

        // Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_SERVICE_NGN, $_SERVER['REQUEST_URI'], $ngn_settings['client_id'], $ngn_settings['pnt']);
        $this->_redirect($_SESSION['back_url']); // Если все нормально делаем редерект
    }
  }

  protected function calcDiscount($in_regpay, $disc)
  {
        /**
        *  Сумма со скидкой.
        *  @param $in_regpay --- рег. плата
        *  @param $disc ---скидка
        */
        return $in_regpay - ($in_regpay * $disc) / 100;
  }
}