<?php
/**
 * My new Zend Framework project
 *
 * @author
 * @version
 */

error_reporting(E_ERROR | E_PARSE);

define('BILLING_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path('.' . PATH_SEPARATOR
                . '../../library/' . PATH_SEPARATOR
                . '../../library/Asr/' . PATH_SEPARATOR
                . '../../library/jquery_php/library/' . PATH_SEPARATOR
                . '../config' . PATH_SEPARATOR
                . '../application/default' . PATH_SEPARATOR
                . '../application/default/models/' . PATH_SEPARATOR
                . '../application/default/helpers/' . PATH_SEPARATOR);

require_once('Initializer.php');
require_once("Zend/Loader/Autoloader.php");

// Set up autoload.
Zend_Loader::registerAutoload();

require_once('Zend/Config/Ini.php');
require_once('Zend/Registry.php');
require_once('Zend/Db.php');
require_once('Zend/Db/Table.php');
require_once('Zend/Debug.php');
require_once('Zend/Auth.php');
require_once('Zend/Session.php');
require_once('Zend/Layout.php');

// setup databases
$config = new Zend_Config_Ini('config.ini','general');

$dbAdapter 	   = Zend_Db::factory($config->db->adapter,$config->db->config->toArray());
$trapAdapter   = Zend_Db::factory($config->trapdatabase->adapter,$config->trapdatabase->config->toArray());
$mysqlAdapter  = Zend_Db::factory($config->mysqldb->adapter, $config->mysqldb->config->toArray());

Zend_Db_Table::setDefaultAdapter($dbAdapter);
Zend_Registry::set('dbAdapter', $dbAdapter);
Zend_Registry::set('mysqlAdapter', $mysqlAdapter);
Zend_Registry::set('trapAdapter', $trapAdapter);

Zend_Registry::set('rekvizits', new Zend_Config_Ini('config.ini','rekvizits'));

$locale = new Zend_Locale('ru_RU');
Zend_Registry::set('Zend_Locale', $locale);

$translate = new Zend_Translate('ini', '../config/russian.ini', 'ru');
Zend_Form::setDefaultTranslator($translate);
Zend_Registry::set('Zend_Translate', $translate);

// setup session
Zend_Session::start();
//error_reporting(E_ALL | E_PARSE);
Zend_Session::setOptions( array('strict'=>true) );

Zend_Layout::startMvc();

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new Initializer('development'));

// Dispatch the request using the front controller.
$frontController->dispatch();
