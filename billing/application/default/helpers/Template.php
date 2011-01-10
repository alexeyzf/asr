<?php

/**
 * Template.php
 *
 * Хелпер Template
 * Строит нужные шаблоны для заведения клиентов
 * Такие как заполнение полей для услуги ADSL, Collocation, Hosting, Dial-up, ISDN
 */

require_once('Zend/Controller/Action.php');

/*
 * Формы
 */
require_once('Zend/Form.php');
require_once('forms/Adsl.php');
require_once('forms/Ivr.php');
require_once('forms/Collacation.php');
require_once('forms/Hosting.php');
require_once('forms/Tasix.php');
require_once('forms/Ngn.php');
require_once('forms/Wifi.php');
require_once('forms/Vpn.php');
require_once('forms/Isdn.php');
require_once('forms/Tradtel.php');
require_once('forms/Dialup.php');
require_once('forms/Lvs.php');
require_once('forms/Pintel.php');
require_once('Zend/Filter/StripTags.php');

/*
 * Модели
 */
require_once('AdslModel.php');
require_once('CollacationModel.php');
require_once('NgnModel.php');
require_once('TarifListModel.php');
require_once('VpnModel.php');
require_once('TradtelModel.php');
require_once('IvrModel.php');

class Zend_Controller_Action_Helper_Template extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {

    }

    /**
    * createForm
    *
    * Creates form and fill it with values
    *
    * @param string $tablelink - table name to create form for
    * @param integer $client_id - Client ID
    * @param array $data - Data to fill form with
    * @return Zend_Form
    */
    public function createForm($tablelink, $client_id, $data)
    {
        // Тип клиента и список тарифов
        $clientModel = new ClientModel();
        $pointModel = new EditPointModel();
        $tarifModel  = new TarifListModel();

        $client = $clientModel->fetchRow('client_id = '. $client_id);
        $point = $pointModel->fetchRecordByID($data['point_id']);

        $list   = $tarifModel->getServiceTarifs($data['servicetype_id'], $point['country_id']);

        if ( $data['tarif_id'] )
        {
        	$currentTarif = $tarifModel->getTarifData($data['tarif_id']);
        	$list[] = $currentTarif;
        }

        $data['list_service'] = $list;

        if ( !$data['startdate'] && !$data['enddate'] )
        {
            $data['startdate'] = date('Y-m-d');
            $data['enddate'] = '2011-01-01';
        }

        $data['client_type_id'] = $client['client_type_id'];
        $data['tablelink']      = $tablelink;


        // Вызываем форму, соответственно в зависимости от услуги.
        // Если услуга collacation то форма будет Form_Collacation и т.д.
        $className = "Form_". ucfirst($tablelink);

        if ( ! class_exists($className) )
        {
            return null;
        }

        $form = new $className();
        $form->populate($data);
        return $form;
    }

    /**
     * createModel
     *
     * Создает модель. В зависимости от значения переменной $tableName
     * у нас будут к примеру такие модели, если услуга ADSL то
     * AdslModel и т.д. для всех услуг
     */
    public function createModel($tableName)
    {

        $modelClassName = ucfirst($tableName). 'Model';

        if ( ! class_exists($modelClassName) )
        {
            return null;
        }

        return new $modelClassName();
    }
}
