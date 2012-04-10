<?php
class Admin_EntrustController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		$this->_tb = Class_Base::_('Entrust');
	}
	public function indexAction()
	{	
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('e'=>'entrust'),'*')
						 ->joinLeft(array('u'=>'users_information'),"e.entrustName = u.id",array('u.userName as enname'))
						 ->joinLeft(array('i'=>'users_information'),"e.userId = i.id",array('i.userName as benname'))
						 ->joinLeft(array('s'=>'step'),"e.stepId = s.id",array('s.content'))
						 ->joinLeft(array('d'=>'detail'),"d.id = s.detailId",array('d.projectName'))
						 ->order('e.entrustTime desc');
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
				  ->joinLeft(array('d'=>'detail'),"s.detailId = d.id",array('projectName'))
				  ->where('s.id = ?',$id);
		$row = $tb->fetchRow($sql)->toArray();
		$selsql = $tb->select(false)->setIntegrityCheck(false)
					 ->from(array('u'=>'users'),array('id'))
					 ->joinLeft(array('i'=>'users_information'),"i.id = u.userName",array('userName'))
					 ->where('u.permissions >= 2');
		$userrow = $tb->fetchAll($selsql)->toArray();
		$usersql = $tb->select(false)->setIntegrityCheck(false)
					  ->from(array('s'=>'step'),array('id'))
					  ->joinLeft(array('k'=>'skill'),"s.skillId = k.skillId",array('userId'))
					  ->joinLeft(array('u'=>'users_information'),"k.userId = u.id",array('userName'))
					  ->where('s.id = ?',$id)
					  ->group('k.userId');
		$rowset = $tb->fetchAll($usersql)->toArray();
		if($this->getRequest()->isPost()) {
			$personname = $this->getRequest()->getParam('personname');
			$userid = $this->getRequest()->getParam('userid');
			$reason = $this->getRequest()->getParam('reason');
			$arrin = array(
					'entrustTime' => date('Y-m-d H:i:s',time()),
					'entrustName' => $_SESSION['USERID'],
					'stepId' => $id,
					'userId' => $userid,
					'personName' => $personname,
					'reason' => $reason
					);
			$this->_tb->insert($arrin);
			$this->_redirect('/admin/individual/index/sid/0');
		}
		$this->view->id=$id;
		$this->view->project = $row['projectName'];
		$this->view->content = $row['content'];
		$this->view->userrow = $userrow;
		$this->view->rowset  = $rowset;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/admin/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/admin/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/admin/individual/sel/', 'method' => 'CreateDetail')));
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
							  ->from(array('e'=>'entrust'),'*')
							  ->joinLeft(array('u'=>'users_information'),"e.entrustName = u.id",array('u.userName as enname'))
							  ->joinLeft(array('i'=>'users_information'),"e.userId = i.id",array('i.userName as benname'))
							  ->joinLeft(array('s'=>'step'),"e.stepId = s.id",array('s.content','s.skillId'))
							  ->joinLeft(array('d'=>'detail'),"d.id = s.detailId",array('d.projectName'))
							  ->where('e.id = ?',$id);
		$row = $this->_tb->fetchRow($selector)->toArray();
		$usersql = $this->_tb->select(false)->setIntegrityCheck(false)
							 ->from(array('s'=>'skill'),array('userId'))
							 ->joinLeft(array('u'=>'users_information'),"s.userId = u.id",array('u.userName'))
							 ->where('s.skillId = ?',$row['skillId'])
							 ->where('s.userId != ?',$row['entrustName']);
		$userrow = $this->_tb->fetchAll($usersql)->toArray();
		$this->_helper->template->head('委托审核流程');
		if($this->getRequest()->isPost() ) {
	    	$state = $this->getRequest()->getParam('state');
	    	$userid = $this->getRequest()->getParam('userid');
	    	$arrup = array(
	    			'state' => $state,
	    			'userId' => $userid
	    			);
	    	$where = 'id = '.$id;
	    	$this->_tb->update($arrup,$where);
	    	$this->_redirect('/admin/entrust/index');
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
		$this->_redirect('/admin/feedback/index/id/'.$deid);
	}
}