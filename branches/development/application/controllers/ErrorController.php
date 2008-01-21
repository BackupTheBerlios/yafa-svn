<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Stream.php';

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $exception = $errors->exception;

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                // ... get some output to display...

            	$this->view->msg = '<pre>HTTP/1.1 404 Not Found' . "\n" .  $exception->getTraceAsString() . '</pre>';
            	
                break;
            default:
                // application error
                switch ( substr($exception->getMessage(),0,3) ) {
                	case '403':
                		// Access denied, so be frindly but strict
                		$this->view->msg = "<h1>Ups</h1><p>An \"Ups\" has occured. That's frindly for \"Access denied\".</p>" . 
                			"<p>But this is not the end. Just go <a href=\"/index/level\">here</a> and learn how you can increase your clearance level.</p>";
                		break;
                	default:
                		// Other error
		                // display error page, but don't change status code
		
		                // Log the exception:
		                $log = new Zend_Log(new Zend_Log_Writer_Stream('/tmp/applicationException.log'));
		                $log->debug($exception->getMessage() . "\n" .  $exception->getTraceAsString());
		                               
		                //Output
		                $this->view->msg = '<pre>' . $exception->getMessage() . "\n" .  $exception->getTraceAsString() . "\nThe error is logged and will be analysed by the developers.</pre>";
		                
		                break;
                }

                break;
        }
    }
}


?>
