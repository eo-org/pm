<?php
require APP_PATH.'/pm/forms/type/Edit.php';
class Pm_TypeController extends Zend_Controller_Action
{
	private $_tb;
	private $_form;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/admin/');
		}
		$this->_form = new Form_Type_Edit();
		$this->_tb = Class_Base::_('Detail_Type');
	}
	
	public function indexAction()
	{	
		$this->_helper->template->head('项目类型列表');
		$hashParam = $this->getRequest()->getParam('hashParam');
		$labels = array(
				'id' => '类型编号',
				'type' => '类型名称',
				'createname' => '创建人',
				'~contextMenu' => '操作'
		);
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
				'labels' => $labels,
				'selectFields' => array(
						'id' => null,
						'desc' => null
				),
				'url' => '/pm/type/get-form-json/',
				'actionId' => 'id',
				'click' => array(
						'action' => 'contextMenu',
						'menuItems' => array(
								array('详情', '/pm/tschedule/index/id/'),
								array('编辑', '/pm/type/edit/id/'),
								array('删除', '/pm/type/del/id/')
						)
				),
				'initSelectRun' => 'true',
				'hashParam' => $hashParam
		));	
		$this->view->partialHTML = $partialHTML;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目类型管理', 'href' => '/pm/type/index/', 'method' => 'ManagementType'),
				array('label' => '类型添加', 'href' => '/pm/type/create/', 'method' => 'CreateType')));
	}
	
	public function createAction()
	{
		$form = $this->_form;
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$type = $this->_tb->insert($form->getValues());
			$this->_redirect('/pm/type/add/id/'.$type);
		}
		$this->view->form = $form;
	}
	
	public function addAction()
	{
		$id = $this->getRequest()->getParam('id');
		$tb = Class_Base::_('Skill_Information');
		$sql = $tb->select(false)
				  ->from($tb,'*');
		$row = $tb->fetchAll($sql)->toArray();
		$this->view->row = $row;
		if($this->getRequest()->isPost()){
			$val = $this->getRequest()->getParam('val');
			$arrbox = explode(":", $val);
			$selval = $this->getRequest()->getParam('selval');
			$arrone = explode(":", $selval);
			for($i=0;$i<count($arrbox)-1;$i++){
				$arrin = array(
						'typeid' => $id,
						'typestep' => $arrbox[$i],
						'skillid' => $arrone[$i]
				);
				$step = Class_Base::_('Typestep');
				$step->insert($arrin);
			}
		}
		$this->view->id=$id;
	}
	
	public function getFormJsonAction()
	{
		$pageSize = 5;
		$selector = $this->_tb->select(false)
					   ->from($this->_tb, '*')
					   ->order('id DESC')
					   ->limitPage(1, $pageSize);
		$result = array();
		foreach($this->getRequest()->getParams() as $key => $value) {
			if(substr($key, 0 , 7) == 'filter_') {
				$field = substr($key, 7);
				switch($field) {
					case 'type':
						$selector->where('type like ?', '%'.$value.'%');
						break;
					case 'createname':
						$selector->where('createname like ?', '%'.$value.'%');
						break;
					case 'page':
						if(intval($value) == 0) {
							$value = 1;
						}
						$selector->limitPage(intval($value), $pageSize);
						$result['currentPage'] = intval($value);
						break;
				}
			}
		}
		$rowset = $this->_tb->fetchAll($selector)->toArray();
		$result['data'] = $rowset;
		$result['dataSize'] = App_Func::count($selector);
		$result['pageSize'] = $pageSize;
	
		if(empty($result['currentPage'])) {
			$result['currentPage'] = 1;
		}
		return $this->_helper->json($result);
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$selector = $this->_tb->find($id)->current();
		$html = $this->_form;
		$html->populate($selector->toArray());	
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams())) {
        	$arrone = $html->getValues();
        	$where = "id = ".$id;
        	$this->_tb->update($arrone, $where);
			$this->_redirect('/pm/type/index/');
		}	
		$this->view->html = $html;
	}
		
	public function delAction()
	{
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$this->_redirect('/pm/type/index/');
	}
}