<?php
require CONTAINER_PATH.'/app/application/admin/forms/Individual/Edit.php';
require CONTAINER_PATH.'/app/application/admin/forms/Page.php';
class Admin_IndividualController extends Zend_Controller_Action
{
	private $_schedule;
	private $_Digits;
	private $_pagelist;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/admin/index/');
		}
		$this->_Digits = new Zend_Filter_Digits();
		$this->_pagelist = new Form_Page();
	}
	public function indexAction()
	{	
		$pagesize = 8;
		$sid = $this->_Digits->filter($this->getRequest()->getParam('sid'),0);
		$page = $this->_Digits->filter($this->getRequest()->getParam('page'),0);
		if($sid == 0){
			$this->_helper->template->head('未完成任务列表');
		}else{
			$this->_helper->template->head('完成任务列表');
		}
		$tb = Class_Base::_('Step');
		$sql = $tb->select(false)
				  ->from($tb,array('count(*) as num'))
				  ->where('programmer = ?',$_SESSION['USERID'])
				  ->where('state = ?',$sid);
		$rowset = $tb->fetchRow($sql)->toArray();
		$ys = $rowset['num'];
		$selector = $tb->select(false)->setIntegrityCheck(false)
					   ->from(array('s' => 'step'), '*')
					   ->joinLeft(array('i' => 'users_information'),"s.programmer = i.id",array('i.userName'))
					   ->joinLeft(array('d' => 'detail'),"s.detailId = d.id", array('projectName'))
					   ->joinLeft(array('e' => 'entrust'),"s.id = e.stepId and e.state = 1",array('e.id as entrustId','e.userId'))
					   ->joinLeft(array('u' => 'users_information'),"e.userId = u.id",array('u.userName as entrustname'))
					   ->where('s.programmer = ? or e.userId  = ?',$_SESSION['USERID'])
					   ->where('s.state = ?',$sid)
					   //->orwhere('e.userid = ?',$_SESSION['USERID'])
					   ->limitPage($page, $pagesize);
		$rowset = $tb->fetchAll($selector)->toArray();
		$this->view->sid = $sid;
		$this->view->rowset = $rowset;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/pm/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/pm/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/pm/individual/sel/', 'method' => 'CreateDetail')));
		$this->view->pageshow = $this->_pagelist->getPage($page,$ys,"/pm/individual/index/sid/".$sid,$pagesize);
	}
	
	public function selAction()
	{
		$this->_helper->template->head('完成任务列表');
		$tb = Class_Base::_('Users_Information');
		$id = $_SESSION['USERID'];
		$row = $tb->find($id)->current()->toArray();
		$tb_skill = Class_Base::_('Skill');
		$sql = $tb_skill->select(false)->setIntegrityCheck(false)
						->from(array('s' => 'skill'),array('skillId'))
						->joinLeft(array('i' => 'skill_information'),"s.skillId = i.id",array('skillName'))
						->where('userId = ?',$id);
		$rowstep = $tb_skill->fetchAll($sql)->toArray();
		$tb_information = Class_Base::_('Skill_Information');
		$selsql = $tb_information->select(false)
								 ->from($tb_information,'*');
		$this->view->skillinformation = $tb_information->fetchAll($selsql)->toArray();
		$this->view->row = $row;
		$this->view->rowset = $rowstep;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/pm/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/pm/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/pm/individual/sel/', 'method' => 'CreateDetail')));
	}
	
	public function getFormJsonAction()
	{
		$sid = $this->_Digits->filter($this->getRequest()->getParam('sid'),0);
		$pageSize = 10;
		$tb = Class_Base::_('Step');
		$selector = $tb->select(false)
					   ->from($tb, '*')
					   ->where('programmer = ?',$_SESSION['USERID'])
					   ->where('state = ?',$sid)
					   ->order('startTime DESC')
					   ->limitPage(1, $pageSize);
		$result = array();
		foreach($this->getRequest()->getParams() as $key => $value) {
			if(substr($key, 0 , 7) == 'filter_') {
				$field = substr($key, 7);
				switch($field) {
					case 'type':
						$selector->where('type like ?', '%'.$value.'%');
						break;
					case 'state':
						$selector->where('state like ?', '%'.$value.'%');
						break;
					case 'label':
						$selector->where('label like ?', '%'.$value.'%');
						break;
					case 'page':
						if(intval($value) == 0) {
							$value = 2;
						}
						$selector = $selector->limitPage(intval($value), $pageSize);
						$result['currentPage'] = intval($value);
						break;
				}
			}
		}
		$rowset = $tb->fetchAll($selector)->toArray();
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
		$form = new Form_Individual_Edit();
		$id = $this->getRequest()->getParam('id');
		$tb = Class_Base::_('Step');
		$row = $tb->find($id)->current();
		$html = $form->populate($row->toArray());
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
        	$row->setFromArray($html->getValues());
			$row->save();
			$this->_redirect('/pm/individual/index/');
		}
		$this->view->html = $html;
	}
	
	public function edituserAction()
	{
		$username = $this->getRequest()->getParam('username');
		$sex = $this->getRequest()->getParam('sex');
		$age = $this->getRequest()->getParam('age');
		$QQ = $this->getRequest()->getParam('QQ');
		$phone = $this->getRequest()->getParam('phone');
		$skillid = $this->getRequest()->getParam('skillid');
		$arrup = array(
				'userName' => $username,
				'sex' => $sex,
				'age' => $age,
				'QQ' => $QQ,
				'phone' => $phone
			);
		$tb = Class_Base::_('Users_Information');
		$id = $_SESSION['USERID'];
		$where = 'id = '.$id;
		$tb->update($arrup, $where);
		$arrbox = explode(":", $skillid);
		$skill = Class_Base::_('Skill');
		$where = 'userId = '.$id;
		$skill->delete($where);
		for($i=0;$i<count($arrbox)-1;$i++){
			$arrin = array(
					'userId' => $id,
					'skillId' => $arrbox[$i]
			);	
			$skill->insert($arrin);
		}
	}
	
	public function editpwdAction()
	{
		if($this->getRequest()->isPost()){
			$newpwd = $this->getRequest()->getParam('newpwd');
			$tb = Class_Base::_('Users');
			$arrup = array(
				'passwd' => $newpwd
			);
			$where = "userName = ".$_SESSION['USERID'];
			$tb->update($arrup, $where);
			$this->_redirect('/pm/individual/sel/');
		}
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/pm/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/pm/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/pm/individual/sel/', 'method' => 'CreateDetail')));
	}
	
}