<?php
require CONTAINER_PATH.'/app/application/admin/forms/Users/Edit.php';
require CONTAINER_PATH.'/app/application/admin/forms/Users/User.php';
require CONTAINER_PATH.'/app/application/admin/forms/Page.php';
class Admin_UsersController extends Zend_Controller_Action
{
	private $_tb;
	private $uid;
	private $_pagelist;
	public function init()
	{
		$this->_tb = Class_Base::_('Users_Information');
		$this->_pagelist = new Form_Page();
	}
	public function indexAction()
	{	
		$pagesize = 3;
		$page = $this->getRequest()->getParam('page');
		$this->_helper->template->head('员工详情列表');
		$selector = $this->_tb->select(false)
							  ->from($this->_tb,'*')
							  ->where('state = ?',1)
							  ->limitPage($page, $pagesize);
		$row = $this->_tb->fetchAll($selector)->toArray();
		$sql = $this->_tb->select(false)
						 ->from($this->_tb,array('count(*) as num'))
						 ->where('state = ?',1);
		$rowset = $this->_tb->fetchRow($sql)->toArray();
		$this->view->row = $row;
		$this->uid = $row[0]['id'];
		$this->view->seldetail = $this->seldetailAction();
		$this->_helper->template->actionMenu(array(
				array('label' => '员工详情管理', 'href' => '/admin/users/index/', 'method' => 'ManagementDetail'),
				array('label' => '员工添加', 'href' => '/admin/users/create/', 'method' => 'CreateDetail'),
				array('label' => '部门管理', 'href' => '/admin/department/index/', 'method' => 'CreateDetail')));
		$this->view->pageshow = $this->_pagelist->getPage($page,$rowset['num'],"/admin/users/index",$pagesize);
	}
	
	public function createAction()
	{
		$form = new Form_Users_Edit();
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$arr = $form->getValues();
			foreach ($arr as $num => $arrone){
				if($num != 'skill'){
					$arruser[$num] = $arrone;
				}
			}
			$id = $this->_tb->insert($arruser);
			$tb = Class_Base::_('Skill');
			foreach ($arr['skill'] as $num => $arrtwo){
				$arrskill = array(
						'userId' => $id,
						'skillId' => $arrtwo
				);
				$tb->insert($arrskill);
			}
			$loginname = $this->getRequest()->getParam('loginname');
			$arrthree = array(
					'userName' => $id,
					'loginName' => $loginname,
					'passwd' => '123456'
					);
			$tb_users = Class_Base::_('Users');
			$tb_users->insert($arrthree);
			$this->_redirect('/admin/users/index/');
		}
		$this->view->html = $form;
	}
	
	public function seldetailAction()
	{
		$uid = $this->getRequest()->getParam('id');
		$h=0;
		if(empty($uid)){
			$uid = $this->uid;
			$h=1;
		}
		$step = Class_Base::_('Step');
		$selector = $step->select(false)->setIntegrityCheck(false)
						 ->from(array('s' => 'step'),array('content'))
						 ->joinLeft(array('d' => 'detail'),"s.detailId = d.id ",array('d.id','d.projectName'))
						 ->joinLeft(array('e' => 'entrust'),"s.id = e.stepId and e.state = 1",array('state'))
						 ->where('programmer = ? or e.userId  = ?',$uid)
						 ->group('detailId');
		$rowdetail = $step->fetchAll($selector)->toArray();
		if($rowdetail){
			$this->view->rowdetail = $rowdetail;
			$detailid = $rowdetail[0]['id'];
			$sql = $step->select(false)->setIntegrityCheck(false)
						->from(array('s' => 'step'),array('id','content','state'))
						->joinLeft(array('e' => 'entrust'),"s.id = e.stepId and e.state = 1",array('e.id as entrustid','e.userId'))
						->joinLeft(array('u' => 'users_information'),"e.userId = u.id",array('userName'))
						->joinLeft(array('i' => 'users_information'),"e.entrustName = i.id",array('userName as busername'))
						->where('programmer = ? or e.userId  = ?',$uid)
						->where('detailId = ?',$detailid);
			$rowstep = $step->fetchAll($sql)->toArray();
			$if = 1;
			$this->view->if = $if;
			$this->view->rowstep = $rowstep;
			$this->view->detailid = $detailid;
			$this->view->uid = $uid;
		}
		$formmenu = $this->view->render('users/sel.phtml');
		if($h==1){
			return $formmenu;
		}else{
			echo  $formmenu;
		}
		exit;
	}
	
	public function selstepAction()
	{
		$did = $this->getRequest()->getParam('did');
		$uid = $this->getRequest()->getParam('uid');
		$step = Class_Base::_('Step');
		$selector = $step->select(false)->setIntegrityCheck(false)
						->from(array('s' => 'step'),array('id','content','state'))
						->joinLeft(array('e' => 'entrust'),"s.id = e.stepId and e.state = 1",array('entrustName','e.userId'))
						->joinLeft(array('u' => 'users_information'),"e.userId = u.id",array('userName'))
						->joinLeft(array('i' => 'users_information'),"e.entrustName = i.id",array('userName as busername'))
						->where('programmer = ? or e.userId  = ?',$uid)
						->where('detailId = ?',$did);
		$row = $step->fetchAll($selector)->toArray();
		//Zend_Debug::dump($row);
		$i =1;
		$selstep = "";
		foreach ($row as $num => $arrone){
			$selstep.= "<li id=".$arrone['id'].">".$i."、".$arrone['content']."(";
			if($arrone['userId']!=''){
				if($uid == $arrone['userId']){
					$selstep.='被'.$arrone['buserName'].'委托、';
				}else {
					$selstep.='委托给'.$arrone['userName'].'、';
				}
			}
			if($arrone['state'] == 0){
				$selstep.='未完成';
			}else {
				$selstep.='完成';
			}
			$selstep.= ")</li>";
			$i++;
		}
		echo $selstep;
		exit;
	}
	
	public function editAction()
	{
		$form = new Form_Users_Edit();
		$id = $this->getRequest()->getParam('id');
		$row = $this->_tb->find($id)->current();
		$tb = Class_Base::_('Skill');
		$sql = $tb->select(false)
					->from($tb,array('skillId'))
					->where('userId = ?',$id);
		$rowstep = $tb->fetchAll($sql)->toArray();
		$this->view->rowset = $rowstep;
		$html = $form->populate($row->toArray());
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
			$arr = $html->getValues();
			$loginname = $this->getRequest()->getParam('loginname');
			foreach ($arr as $num => $arrone){
				if($num != 'skill'){
					$arruser[$num] = $arrone;
				}
			}
			$where = 'id = '.$id;
			$this->_tb->update($arruser, $where);
			$where = 'userId = '.$id;
			$tb->delete($where);
			foreach ($arr['skill'] as $num => $arrtwo){
				$arrskill = array(
					'userId' => $id,
					'skillId' => $arrtwo		
				);
				$tb->insert($arrskill);
			}
			$arrthree = array(
					'loginName' => $loginname,
					'passwd' => '123456'
			);
			$tb_users = Class_Base::_('Users');
			$where = 'userName = '.$id;
			$tb_users->update($arrthree, $where);
			$this->_redirect('/admin/users/index/');
		}
		$this->view->html = $html;
	}
	
	public function selusernameAction()
	{
		$User = new Form_Users_User();
		$uname = $this->getRequest()->getParam('uname');
		$loginname =  strtolower($User->getInitials($uname));
		$tb = Class_Base::_('Users');
		$sql = $tb->select(false)->setIntegrityCheck(false)
				  ->from(array('u' => 'users'),'*')
				  ->joinLeft(array('i' => 'users_information'),"u.userName = i.id",array('userName'))
				  ->where('loginName = ?',$loginname);
		$row = $tb->fetchRow($sql);
		if(!empty($row)){
			$aa = "用户登陆账号:<input id='loginname' type='text' value='".$row['loginName']."' name='loginname' style='ime-mode:disabled;' />";
			$aa.= "<div id='pd' style='float:right;'>×</div><div id='jl' style='float:left;'>该账号已被".$row['userName']."使用</div>";
		}else{
			$aa = "用户登陆账号:<input id='loginname' readonly='readonly' type='text' value='".$loginname."' name='loginname' />";
			$aa.= "<div id='pd' style='float:right;'>√</div><div id='jl' style='float:left;'>该账号可以使用</div>";
		}
		echo $aa;
		exit;
	}
	
	public function resetpwdAction(){
		$id = $this->getRequest()->getParam('id');
		$where ="userName = ".$id;
		$tb = Class_Base::_('Users');
		$data = array(
			'passwd' => '123456'
		);
		$tb->update($data, $where);
		exit;
	}
	
	public function deluserAction()
	{
		$id = $this->getRequest()->getParam('id');
		$where ='id = '.$id;
		$date = array('state' => 0);
		$this->_tb->update($date,$where);
		$where = 'userName = '.$id;
		$tb = Class_Base::_('Users');
		$tb->delete($where);
		$tbskill = Class_Base::_('Skill');
		$where = 'userId = '.$id;
		$tb->delete($where);
		$this->_redirect('/admin/users/index/');
	}
}