<?php
set_include_path('/home/yafa/php' . PATH_SEPARATOR . get_include_path());

require_once '../config.php';
require_once '../application/menu.php';

// Zend Framework Controller:
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Config.php';
require_once '../MP/Menu.php';
require_once '../MP/Auth.php';

$front = Zend_Controller_Front::getInstance();
$front->setParam('config', new Zend_Config($configArray))
      ->setControllerDirectory($front->getParam('config')->BasePath . '/application/controllers')
      ->setRouter(new Zend_Controller_Router_Rewrite())
      ->registerPlugin(new MP_Menu())
      ->setParam('menu', $myMenu)
      ->setParam('auth', new MP_Auth());
$front->dispatch();

?>
