<?php
class Admin_ActionMenu extends App_Brick_Fixed_Abstract
{
	public $menuArr = array();
    
    public function setMenu($menuArr)
    {
    	if(is_array($menuArr)) {
    		$this->menuArr = $menuArr;
    	}
    }
    
    protected function _getExtName()
    {
    	return "Admin_ActionMenu";
    }
    
    public function prepare()
    {
		$buttonArr = array();
		foreach($this->menuArr as $key => $setting) {
			$label = '';
			$method = '';
			$href = null;
			if(is_array($setting)) {
				$label = $setting['label'];
				$href = $setting['href'];
				$method = isset($setting['method']) ? $setting['method'] : 'link';
			} else {
				$type = $setting;
				$urlHelper = new Zend_View_Helper_Url();
				switch($type) {
					case 'create':
						$label = '创建新项目';
						$method = '';
						if(empty($href)) {
							$href = $urlHelper->url(array('action' => 'create'));
						}
						break;
					case 'save':
						$label = '保存';
						$method = 'post';
						if(empty($href)) {
							$href = $urlHelper->url();
						}
						break;
					case 'delete':
						$label = '删除';
						$method = '';
						if(empty($href)) {							
							$href = $urlHelper->url(array('action' => 'delete'));
						}
						break;
					default:
						throw new Exception('action menu not defined by: "'.$type.'"');
				}
			}
			
    		$controller = Zend_Controller_Front::getInstance();
    		$request = $controller->getRequest();
    		
    		//direct actions from normal page
    		if(!$request->isXmlHttpRequest()) {
				if(empty($method)) {
					$buttonArr[] = "<a class='action-menu' href='".$href."'>".$label."</a>";				
				} else {
					$buttonArr[] = "<a class='action-menu' href='".$href."' method='".$method."'>".$label."</a>";
				}
			//actions called from within lightbox
			} else {
				if(empty($method)) {
					$buttonArr[] = "<a class='action-menu' href='#".$href."'>".$label."</a>";
				} else if($method == 'post') {
					$buttonArr[] = "<a class='action-menu' href='".$href."' method='ajax-post'>".$label."</a>";
				} else {
					$buttonArr[] = "<a class='action-menu' href='".$href."' method='".$method."'>".$label."</a>";
				}
			}
		}
		$this->view->buttons = $buttonArr;
    }
}