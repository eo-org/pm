<?php
define("AP_PATH", "D://xampp/htdocs/form/include");
define("CONTAINER_PATH", 'D://xampp/htdocs/form/service-form');
define("APP_PATH", CONTAINER_PATH.'/app/application');

define("APP_ENV", getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
define("LOCAL_CHARSET", getenv('LOCAL_CHARSET') ? getenv('LOCAL_CHARSET') : 'UTF-8');
//set_include_path(LIB_PATH);
set_include_path(AP_PATH.PATH_SEPARATOR.CONTAINER_PATH.'/library');

require_once AP_PATH."/Zend/Application.php";
$application = new Zend_Application(APP_ENV, APP_PATH.'/configs/application.ini');
$application->bootstrap()->run();
