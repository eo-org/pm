<?php
require CONTAINER_PATH.'/app/application/pm/forms/Detail/Edit.php';
class Pm_DepartmentController extends Zend_Controller_Action
{
	private $_tb;
	private $sid;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/index/');
		}
		$this->_tb = Class_Base::_('Skill_Information');
	}
	public function indexAction()
	{	
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('i' => 'skill_information'),'*')
						 ->joinLeft(array('s' => 'skill'),"i.id = s.skillid ",array('count(s.userid) as num'))
						 ->group('i.id');
		$row = $this->_tb->fetchAll($selector);
		if(!empty($row)){
			$row = $row->toArray();
			$this->sid = $row[0]['id'];
		}
		$this->view->row = $row;
		$this->view->usersinformation = $this->ufAction();
		$this->_helper->template->actionMenu(array(
				array('label' => '部门添加', 'href' => '/pm/detail/create/', 'method' => 'CreateDetail')));
	}
	
	public function ufAction()
	{
		$sid = $this->getRequest()->getParam('id');
		$h=0;
		if(empty($sid)){
			$sid = $this->sid;
			$h=1;
		}
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
							  ->from(array('s' => 'skill'))
							  ->joinLeft(array('i' => 'users_information'),"s.userid = i.id",array('username'))
							  ->where('s.skillid = ?',$sid)
							  ->where('i.state = ?',1);
		$rowskill = $this->_tb->fetchAll($selector)->toArray();
		$this->view->rowskill = $rowskill;
		$formmenu = $this->view->render('department/uf.phtml');
		if($h==1){
			return $formmenu;
		}else{
			echo  $formmenu;
		}
		exit;
	}
	
	public function selAction()
	{
		$id = $this->getRequest()->getParam('id');
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
							  ->from(array('i' => 'skill_information'),'*')
							  ->joinLeft(array('s' => 'skill'),"i.id = s.skillid ",array('count(s.userid) as num'))
							  ->where('i.id = ?',$id);
		$row = $this->_tb->fetchRow($selector)->toArray();
		$aa ="<td align='center' height='35' class='".$id."' id='users'>".$row['skillname']."</td><td id='num' align='center'>".$row['num']."</td>";
		$aa.="<td align='center'><a class='editdepar' id='".$id."' href=''>修改</a>&nbsp;&nbsp;<a class='deldepar' id='".$id."' href=''>删除</a></td>";
		echo $aa;
		exit;
	}
	
	public function addAction()
	{
		if($this->getRequest()->isPost()) {
			$val = $this->getRequest()->getParam('val');
			$arrbox = explode(":", $val);
			for($i=0;$i<count($arrbox)-1;$i++){
				$arrin = array(
						'skillname' => $arrbox[$i]
				);
				$id = $this->_tb->insert($arrin);
				$aa ="<td align='center' height='35' class='".$id."' id='users'>".$arrbox[$i]."</td><td id='num' align='center'>0</td>";
				$aa.="<td align='center'><a class='editdepar' id='".$id."' href=''>修改</a>&nbsp;&nbsp;<a class='deldepar' id='".$id."' href=''>删除</a></td>";
			}
		}
		echo $aa;
		exit;
	}	
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$skillname = $this->getRequest()->getParam('skillname');
		$arrup = array(
				'skillname' => $skillname
				);
		$where = 'id = '.$id;
		$this->_tb->update($arrup,$where);
	}
	
	public function delAction()
	{
		$id = $this->getRequest()->getParam('id');
		$where = "id = ".$id;
		$this->_tb->delete($where);
		$tb = Class_Base::_('Skill');
		$where = "skillid = ".$id;
		$tb->delete($where);
		exit;
	}
}