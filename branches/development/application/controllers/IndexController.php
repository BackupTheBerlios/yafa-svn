<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    	$this->view->realname = Zend_Controller_Front::getInstance()->getParam('auth')->realname;
    	$this->view->level = Zend_Controller_Front::getInstance()->getParam('auth')->level;
    }

    public function levelAction()
    {
    	$this->view->realname = Zend_Controller_Front::getInstance()->getParam('auth')->realname;
    	$this->view->level = Zend_Controller_Front::getInstance()->getParam('auth')->level;
    }
    
    // Unused
    public function addAction()
    {
        // Render 'index/form.phtml' instead of 'index/add.phtml'
        $this->_helper->viewRenderer('form');
    }
    
    // Unused
    public function editAction()
    {
        // Render 'index/form.phtml' instead of 'index/edit.phtml'
        $this->_helper->viewRenderer('form');
        // Use default value of 1 if id is not set
        $pic = $this->_getParam('pic', 12);

        //Give pic to view object
        $this->view->pic = $pic;
    }

    // Unused
    public function processAction()
    {
        // do some validation...
        $pic = $this->_getParam('pic', 12);
        if ( $pic >= 10 ) {
            // Render 'index/form.phtml' instead of 'index/process.phtml'
            $this->view->msg = "Numbär to big!";
            $this->editAction();
            return;
        }

        // otherwise continue processing...
        $this->view->pic = $pic;
        
    }
}


?>
