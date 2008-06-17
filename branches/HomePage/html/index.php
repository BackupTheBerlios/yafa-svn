<?php

require_once '../config.php';
require_once '../application/menu.php';

// Zend Framework Controller:
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Config.php';
require_once 'Classes/Layout.php';

$front = Zend_Controller_Front::getInstance();
$front->setParam('config', new Zend_Config($configArray))
      ->setControllerDirectory($front->getParam('config')->BasePath . '/application/controllers')
      ->setRouter(new Zend_Controller_Router_Rewrite())
      ->registerPlugin(new Layout());

require_once 'Zend/Db.php';
$db = Zend_Db::factory($front->getParam('config')->database);
require_once '../MP/Zend_DB_Table_Abstract.php';
Zend_Db_Table_Abstract::setDefaultAdapter($db);
require_once '../application/models/_init.php';

$front->dispatch();
?>
