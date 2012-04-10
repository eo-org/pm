<?php
class Helper_Extension extends Zend_Controller_Action_Helper_Abstract
{
	public function extension($extName)
	{
		include_once BASE_PATH.'/extension/common/'.$extName.'/'.$extName.'.php';
		$brick = new ReflectionClass($extName);
		
		$num = func_num_args();
		if($num > 1) {
			$args = func_get_args();
			array_shift($args);
			return $brick->newInstanceArgs($args)->render();
		} else {
			return $brick->newInstanceArgs()->render();
		}
	}
}