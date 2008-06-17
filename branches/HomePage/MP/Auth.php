<?php
require_once 'Zend/Db.php';

class MP_Auth
{
	public $username = '';
	public $realname = '';
	public $level = 0;
	
	public function __construct()
    {
        $this->username = $_SERVER['REMOTE_USER'];

        $this->db = Zend_Db::factory(Zend_Controller_Front::getInstance()->getParam('config')->database);
		$result = $this->db->fetchAll("SELECT * FROM users WHERE username = ?", $this->username );
		if ($result) {
        	$this->level = $result[0]['level'];
        	$this->realname = $result[0]['realname'];
		}
    }
    
    public function auth($required_level)
    {
    	if ($required_level > $this->level)
    	{
   			throw new Exception('403 Access Denied!');
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    public function check($required_level)
    {
    	return ($required_level > $this->level);
    }
}

?>