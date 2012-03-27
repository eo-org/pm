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
	
// 	protected function _initAutoload()
// 	{
// 		require_once 'Zend/Application/Module/Bootstrap.php';
// 		$resourceLoader = new zend_application_module_Bootstrap();
// 		$resourceLoader -> addResourceType("Base", "bases", "Base");
// 		return $resourceLoader;
// 	}
	
	protected function _initDb()
	{
		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host' => 'localhost',
			'username' => 'root',
			'password' => '',
			'dbname' => 'project',
			'adapter' => 'mysqli',
			'charset' => 'UTF8'
		));
		Zend_Registry::set('db', $db);
		Zend_Db_Table::setDefaultAdapter($db);
	}
	
	protected function _initSession()
	{
		Zend_Session::start();
	}
	
    protected function _initController()
    {
    	
    	Zend_Controller_Action_HelperBroker::addPath(APP_PATH.'/helpers', 'Helper');
//    	
        $controller = Zend_Controller_Front::getInstance();
//        
        $controller->setControllerDirectory(array(
            'default' => APP_PATH.'/default/controllers',
        	'admin' => APP_PATH.'/admin/controllers',
            'rest' => APP_PATH.'/rest/controllers',
        	'project' => APP_PATH.'/project/controllers',
        	'front' =>APP_PATH.'/front/controllers')
     	 );
        
        
        $controller->throwExceptions(true);
//                
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('template');
//        
//        $controller->registerPlugin(new Class_Plugin_Acl());
        $controller->registerPlugin(new App_Plugin_LayoutSwitch($layout, array('admin')));
//        $controller->registerPlugin(new Class_Plugin_BrickRegister($layout));
//        
//		$view = new Zend_View();
//		$view->headTitle()->setSeparator('_');
//		$tb = new Zend_Db_Table('site_general');
//		$row = $tb->fetchAll()->current();
//		if(!is_null($row->pageTitle)) {
//			$view->headTitle($row->pageTitle);
//		}
//		if(!empty($row->metakey)) {
//			$view->headMeta()->appendName('keywords', $row->metakey);
//		}
//		if(!empty($row->metadesc)) {
//			$view->headMeta()->appendName('description', $row->metadesc);
//		}
    }
    
    protected function _initRouter()
    {
    	$controller = Zend_Controller_Front::getInstance();
    	$router = $controller->getRouter();
        $router->addRoute('rest', new Zend_Rest_Route($controller, array(), array('rest')));
    	
//        $controller = $this->getPluginResource('frontController')->getFrontController();
//        $router = $controller->getRouter();
//        $router->addRoute('article', new Zend_Controller_Router_Route_Regex(
//            'article-(\d+)\.shtml',
//            array('controller' => 'article'),
//            array(
//                1 => 'action'
//            ),
//            'article-%1$s.shtml'
//        ));
//        $router->addRoute('list', new Zend_Controller_Router_Route_Regex(
//            'list-(\d+)/page(\d+)\.shtml',
//            array(
//                'controller' => 'list',
//            	'page' => 1),
//            array(
//                1 => 'action',
//                2 => 'page'
//            ),
//            'list-%1$s/page%2$s.shtml'
//        ));
//        $router->addRoute('product', new Zend_Controller_Router_Route_Regex(
//            'product-(\d+)\.shtml',
//            array('controller' => 'product'),
//            array(
//                1 => 'action'
//            ),
//            'product-%1$s.shtml'
//        ));
//        $router->addRoute('product-list', new Zend_Controller_Router_Route_Regex(
//            'product-list-(\d+)/page(\d+)\.shtml',
//            array(
//                'controller' => 'product-list',
//            	'page' => 1),
//            array(
//                1 => 'action',
//                2 => 'page'
//            ),
//            'product-list-%1$s/page%2$s.shtml'
//        ));
//        unset($router);
    }
}