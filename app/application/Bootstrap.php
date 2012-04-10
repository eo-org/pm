<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoloader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Class_');
		$autoloader->registerNamespace('Twig_');
		$autoloader->registerNamespace('App_');
	}
	
	protected function _initDb()
	{
		$db = $this->getPluginResource('db')->getDbAdapter();
        $db->query("SET NAMES 'utf8'");
		
		Zend_Registry::set('db', $db);
		Zend_Db_Table::setDefaultAdapter($db);
	}
	
	protected function _initSession()
	{
		Zend_Session::start();
		Class_Server::config(APP_ENV);
	}
	
    protected function _initController()
    {
    	Zend_Controller_Action_HelperBroker::addPath(APP_PATH.'/helpers', 'Helper');
        $controller = Zend_Controller_Front::getInstance();
        $controller->setControllerDirectory(array(
            'default' => APP_PATH.'/default/controllers',
        	'admin' => APP_PATH.'/admin/controllers',
        	'pm' => APP_PATH.'/pm/controllers',
        	'rest' => APP_PATH.'/rest/controllers'
        ));
        
        $csu = Class_Session_User::getInstance();
        $controller->registerPlugin(new App_Plugin_BackendSsoAuth(
        	$csu,
        	Class_Server::getSiteUrl().'/admin',
        	'service-pm',
        	Class_Server::API_KEY
        ));
        
        $controller->throwExceptions(true);
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('template');
    }
    
    protected function _initRouter()
    {
    	$controller = Zend_Controller_Front::getInstance();
    	$router = $controller->getRouter();
        $router->addRoute('rest', new Zend_Rest_Route($controller, array(), array('rest')));
    }
}