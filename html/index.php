<?php
//set path evironment
define("BASE_PATH", getenv('BASE_PATH'));
define("CONTAINER_PATH", BASE_PATH.'/service-pm');
define("APP_PATH", CONTAINER_PATH.'/app/application');

//set const data
define("APP_ENV", getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
define("LOCAL_CHARSET", getenv('LOCAL_CHARSET') ? getenv('LOCAL_CHARSET') : 'UTF-8');

$libPath = BASE_PATH.'/include';
$commonLibPath = BASE_PATH.'/libraries/common';
$pmLibPath = BASE_PATH.'/libraries/service-pm';
set_include_path($libPath.PATH_SEPARATOR.$commonLibPath.PATH_SEPARATOR.$pmLibPath);

require_once $libPath."/Zend/Application.php";
$application = new Zend_Application(APP_ENV, BASE_PATH.'/configs/pm/application.ini');
$application->bootstrap()->run();
