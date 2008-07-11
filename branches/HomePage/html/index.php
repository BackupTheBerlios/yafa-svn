<?php

require_once '../config.php';

// Zend Framework Controller:
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Config.php';
require_once 'Classes/Layout.php';

$front = Zend_Controller_Front::getInstance();
$front->setParam('config', new Zend_Config($configArray));
$front->setParam('log', new Zend_Log(new Zend_Log_Writer_Stream($front->getParam('config')->BasePath . '/log/debug.log')));
$front->setControllerDirectory($front->getParam('config')->BasePath . '/application/controllers');
$front->setRouter(new Zend_Controller_Router_Rewrite());

Zend_Controller_Front::getInstance()->getParam('log')->debug("index: register Layout");
$front->registerPlugin(new Layout());;

Zend_Controller_Front::getInstance()->getParam('log')->debug("index: DB_init");
require_once 'Zend/Db.php';
$db = Zend_Db::factory($front->getParam('config')->database);
require_once '../MP/Zend_DB_Table_Abstract.php';
Zend_Db_Table_Abstract::setDefaultAdapter($db);
require_once '../application/models/_init.php';

Zend_Controller_Front::getInstance()->getParam('log')->debug("index: dispatch");
$front->dispatch();
?>
