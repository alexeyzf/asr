<?php
// Define path to application directory
error_reporting(E_ERROR | E_PARSE);

defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
 
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));
 
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../../library'),
    realpath('../../library/Asr/'),
    realpath('../../library/jquery_php/library/'),
    realpath('../application/default'),
    realpath('../application/default/models/'),
    realpath('../application/default/helpers/'),
    get_include_path(),
)));

require_once("Zend/Loader/Autoloader.php");
Zend_Loader::registerAutoload();
 
/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'ControllerTestCase.php';

$locale = new Zend_Locale('ru_RU');
Zend_Registry::set('Zend_Locale', $locale);

// Указываем Zend-у откуда брать хелперы
Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/default/helpers');

$translate = new Zend_Translate('ini', '../config/russian.ini', 'ru');
Zend_Form::setDefaultTranslator($translate);
Zend_Registry::set('Zend_Translate', $translate);