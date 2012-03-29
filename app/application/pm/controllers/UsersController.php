<?php
require CONTAINER_PATH.'/app/application/pm/forms/Users/Edit.php';
require CONTAINER_PATH.'/app/application/pm/forms/Users/User.php';
class Pm_UsersController extends Zend_Controller_Action
{
	private $_tb;
	private $uid;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/index/');
		}
		$this->_tb = Class_Base::_('Users_Information');
	}
	public function indexAction()
	{	
		$this->_helper->template->head('员工详情列表');
		$selector = $this->_tb->select(false)
							  ->from($this->_tb,'*')
							  ->where('state = ?',1);
		$row = $this->_tb->fetchAll($selector)->toArray();
		$this->view->row = $row;
		$this->uid = $row[0]['id'];
		$this->view->seldetail = $this->seldetailAction();
		$this->_helper->template->actionMenu(array(
				array('label' => '员工详情管理', 'href' => '/pm/users/index/', 'method' => 'ManagementDetail'),
				array('label' => '员工添加', 'href' => '/pm/users/create/', 'method' => 'CreateDetail'),
				array('label' => '部门管理', 'href' => '/pm/department/index/', 'method' => 'CreateDetail')));
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
						'userid' => $id,
						'skillid' => $arrtwo
				);
				$tb->insert($arrskill);
			}
			$loginname = $this->getRequest()->getParam('loginname');
			$arrthree = array(
					'username' => $id,
					'loginname' => $loginname,
					'passwd' => '123456'
					);
			$tb_users = Class_Base::_('Users');
			$tb_users->insert($arrthree);
			$this->_redirect('/pm/users/index/');
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
						 ->from(array('s' => 'step'))
						 ->joinLeft(array('d' => 'detail'),"s.detailid = d.id ",array('id','projectname'))
						 ->where('programmer = ?',$uid)
						 ->group('detailid');
		$rowdetail = $step->fetchAll($selector)->toArray();
		if($rowdetail){
			$this->view->rowdetail = $rowdetail;
			$detailid = $rowdetail[0]['id'];
			$sql = $step->select(false)
						->from($step,array('id','content','state'))
						->where('programmer = ?',$uid)
						->where('detailid = ?',$detailid);
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
		$selector = $step->select(false)
						 ->from($step,array('id','content','state'))
						 ->where('detailid = ?',$did)
						 ->where('programmer =?',$uid);
		$row = $step->fetchAll($selector)->toArray();
		$i =1;
		$selstep = "";
		foreach ($row as $num => $arrone){
			$selstep.= "<li id=".$arrone['id'].">".$i."、".$arrone['content']."(";
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
					->from($tb,array('skillid'))
					->where('userid = ?',$id);
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
			$where = 'userid = '.$id;
			$tb->delete($where);
			foreach ($arr['skill'] as $num => $arrtwo){
				$arrskill = array(
					'userid' => $id,
					'skillid' => $arrtwo		
				);
				$tb->insert($arrskill);
			}
			$arrthree = array(
					'loginname' => $loginname,
					'passwd' => '123456'
			);
			$tb_users = Class_Base::_('Users');
			$where = 'username = '.$id;
			$tb_users->update($arrthree, $where);
			$this->_redirect('/pm/users/index/');
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
				  ->joinLeft(array('i' => 'users_information'),"u.username = i.id",array('username'))
				  ->where('loginname = ?',$loginname);
		$row = $tb->fetchRow($sql);
		if(!empty($row)){
			$aa = "用户登陆账号:<input id='loginname' type='text' value='".$row['loginname']."' name='loginname' style='ime-mode:disabled;' />";
			$aa.= "<div id='pd' style='float:right;'>×</div><div id='jl' style='float:left;'>该账号已被".$row['username']."使用</div>";
		}else{
			$aa = "用户登陆账号:<input id='loginname' readonly='readonly' type='text' value='".$loginname."' name='loginname' />";
			$aa.= "<div id='pd' style='float:right;'>√</div><div id='jl' style='float:left;'>该账号可以使用</div>";
		}
		echo $aa;
		exit;
	}
	
	public function resetpwdAction(){
		$id = $this->getRequest()->getParam('id');
		$where ="username = ".$id;
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
		$where = 'username = '.$id;
		$tb = Class_Base::_('Users');
		$tb->delete($where);
		$this->_redirect('/pm/users/index/');
	}
}