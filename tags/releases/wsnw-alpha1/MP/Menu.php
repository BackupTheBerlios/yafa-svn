<?php
require_once 'Zend/Controller/Plugin/Abstract.php';

class MP_Menu extends Zend_Controller_Plugin_Abstract
{
/*    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody("<p>routeStartup() called</p>\n");
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody("<p>routeShutdown() called</p>\n");
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody("<p>dispatchLoopStartup() called</p>\n");
    }*/

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $view = new Zend_View();
        $view->setBasePath(Zend_Controller_Front::getInstance()->getParam('config')->BasePath . '/application/views');
		$view->menu = Zend_Controller_Front::getInstance()->getParam('menu');
		$view->realname = Zend_Controller_Front::getInstance()->getParam('auth')->realname;
		$view->username = Zend_Controller_Front::getInstance()->getParam('auth')->username;
    	$view->level = Zend_Controller_Front::getInstance()->getParam('auth')->level;
		
        $response->prepend('header', $view->render('header.phtml'));
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $view = new Zend_View();
        $view->setBasePath(Zend_Controller_Front::getInstance()->getParam('config')->BasePath . '/application/views');

        $response->append('footer', $view->render('footer.phtml'));
    }

/*    public function dispatchLoopShutdown()
    {
        $this->getResponse()->appendBody("<p>dispatchLoopShutdown() called</p>\n");
    }*/
}
?>
