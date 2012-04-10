<?php
require CONTAINER_PATH.'/app/application/admin/forms/Admin/Index.php';
class Admin_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_form = new Form_Admin_Index();
		$this->_tb = Class_Base::_('Users');
	}
	
	public function indexAction()
	{
		$form = $this->_form;
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$arrin = $form->getValues();
			$selector = $this->_tb->select(false)
							 ->from($this->_tb, '*')
							 ->where('loginName = ?',$arrin['username'])
				 			 ->where('passwd = ?',$arrin['passwd']);
			$rowset = $this->_tb->fetchRow($selector);
			if(!empty($rowset)){
				$rowset = $rowset->toArray();
				$tb = Class_Base::_('Users_Information');
				$sql = $tb->select(false)
						  ->from($tb,array('userName'))
						  ->where('id = ?',$rowset['userName']);
				$row = $tb->fetchRow($sql)->toArray();
				$_SESSION['USERID'] = $rowset['userName'];
				$_SESSION['USERNAME'] = $row['userName'];
				$_SESSION['PERMISSIONS'] = $rowset['permissions'];
				//$this->_helper->redirector->gotoSimple('/pm/detail/index');
				$this->_redirect('/pm/detail/index');
			}
			$this->view->errorMsg = "用户名或密码错误";
		}
		$this->view->form = $form;
	}
	
	public function closeAction()
	{
		session_destroy();
		//$this->_hepler->redirector->gotoSimple('index','index','pm');
		$this->_redirect('/pm/index/index');
	}
	
	public function selpwdAction()
	{
		$oldpwd = $this->getRequest()->getParam('oldpwd');
		$selector = $this->_tb->select(false)
							  ->from($this->_tb, '*')
							  ->where('userName = ?',$_SESSION['USERID']);
		$rowset = $this->_tb->fetchRow($selector);
		if(!empty($rowset)){
			$rowset = $rowset->toArray();
			if($oldpwd == $rowset['passwd']){
				echo 1;
			}else{
				echo 0;
			}
		}else{echo 0;}
		exit;
	}
}
