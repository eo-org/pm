<?php
class Admin_Header extends Class_Brick_Solid_Abstract
{
	public function prepare()
	{
		$homeLabel = "后台管理系统";
		
		$siteInfo = Zend_Registry::get('siteInfo');
		if($siteInfo['type'] == 'multiple') {
			$homeLabel.= ' - '.$siteInfo['subdomain']['label'];
		}
		$this->view->homeLabel = $homeLabel;
	}
}