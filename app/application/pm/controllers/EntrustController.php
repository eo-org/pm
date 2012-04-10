<?php
class Pm_EntrustController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/index/');
		}
		$this->_tb = Class_Base::_('Entrust');
	}
	public function indexAction()
	{	
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('e'=>'entrust'),'*')
						 ->joinLeft(array('u'=>'users_information'),"e.entrustname = u.id",array('u.username as enname'))
						 ->joinLeft(array('i'=>'users_information'),"e.userid = i.id",array('i.username as benname'))
						 ->joinLeft(array('s'=>'step'),"e.stepid = s.id",array('s.content'))
						 ->joinLeft(array('d'=>'detail'),"d.id = s.detailid",array('d.projectname'))
						 ->order('e.detime desc');
		$row = $this->_tb->fetchAll($selector)->toArray();
		$this->_helper->template->head('委托流程详情列表');
		$this->view->row = $row;
		//Zend_Debug::dump($row);
	}
	
	public function createAction()
	{
		$id = $this->getRequest()->getParam('id');
		$tb = Class_Base::_('Step');
		$sql = $tb->select(false)->setIntegrityCheck(false)
				  ->from(array('s'=>'step'),array('content'))
				  ->joinLeft(array('d'=>'detail'),"s.detailid = d.id",array('projectname'))
				  ->where('s.id = ?',$id);
		$row = $tb->fetchRow($sql)->toArray();
		$selsql = $tb->select(false)->setIntegrityCheck(false)
					 ->from(array('u'=>'users'),array('id'))
					 ->joinLeft(array('i'=>'users_information'),"i.id = u.username",array('username'))
					 ->where('u.permissions >= 2');
		$userrow = $tb->fetchAll($selsql)->toArray();
		$usersql = $tb->select(false)->setIntegrityCheck(false)
					  ->from(array('s'=>'step'),array('id'))
					  ->joinLeft(array('k'=>'skill'),"s.skillid = k.skillid",array('userid'))
					  ->joinLeft(array('u'=>'users_information'),"k.userid = u.id",array('username'))
					  ->where('s.id = ?',$id)
					  ->group('k.userid');
		$rowset = $tb->fetchAll($usersql)->toArray();
		if($this->getRequest()->isPost()) {
			$personname = $this->getRequest()->getParam('personname');
			$userid = $this->getRequest()->getParam('userid');
			$reason = $this->getRequest()->getParam('reason');
			$arrin = array(
					'detime' => date('Y-m-d H:i:s',time()),
					'entrustname' => $_SESSION['USERID'],
					'stepid' => $id,
					'userid' => $userid,
					'personname' => $personname,
					'reason' => $reason
					);
			$this->_tb->insert($arrin);
			$this->_redirect('/pm/individual/index/sid/0');
		}
		$this->view->id=$id;
		$this->view->project = $row['projectname'];
		$this->view->content = $row['content'];
		$this->view->userrow = $userrow;
		$this->view->rowset  = $rowset;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/pm/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/pm/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/pm/individual/sel/', 'method' => 'CreateDetail')));
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
							  ->from(array('e'=>'entrust'),'*')
							  ->joinLeft(array('u'=>'users_information'),"e.entrustname = u.id",array('u.username as enname'))
							  ->joinLeft(array('i'=>'users_information'),"e.userid = i.id",array('i.username as benname'))
							  ->joinLeft(array('s'=>'step'),"e.stepid = s.id",array('s.content','s.skillid'))
							  ->joinLeft(array('d'=>'detail'),"d.id = s.detailid",array('d.projectname'))
							  ->where('e.id = ?',$id);
		$row = $this->_tb->fetchRow($selector)->toArray();
		$usersql = $this->_tb->select(false)->setIntegrityCheck(false)
							 ->from(array('s'=>'skill'),array('userid'))
							 ->joinLeft(array('u'=>'users_information'),"s.userid = u.id",array('u.username'))
							 ->where('s.skillid = ?',$row['skillid'])
							 ->where('s.userid != ?',$row['entrustname']);
		$userrow = $this->_tb->fetchAll($usersql)->toArray();
		$this->_helper->template->head('委托审核流程');
		if($this->getRequest()->isPost() ) {
	    	$state = $this->getRequest()->getParam('state');
	    	$userid = $this->getRequest()->getParam('userid');
	    	$arrup = array(
	    			'state' => $state,
	    			'userid' => $userid
	    			);
	    	$where = 'id = '.$id;
	    	$this->_tb->update($arrup,$where);
	    	$this->_redirect('/pm/entrust/index');
		}
		$this->view->id= $id;
		$this->view->row = $row;
		$this->view->userrow = $userrow;	
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$this->_redirect('/pm/feedback/index/id/'.$deid);
	}
}