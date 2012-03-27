<?php
class Helper_Template extends Zend_Controller_Action_Helper_Abstract
{
	public function assign($name, $value)
	{
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		
		$view->{$name} = $value;
	}
	
	public function head($value)
	{
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		
		$view->head = $value;
		return $this;
	}
	
	public function actionMenu($menu)
	{
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		
		$view->actionMenu = $menu;
		return $this;
	}
	
	/**
	 * 
	 * protal links, those are link tabs on the top right corner of the page
	 * @param Array $portalArr, array(label, controllerName)
	 */
	public function portal($portalArr)
	{
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		
		$view->portalArr = $portalArr;
		return $this;
	}
}