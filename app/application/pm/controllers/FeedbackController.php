<?php
class Pm_FeedbackController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/index/');
		}
		$this->_tb = Class_Base::_('Feedback');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('f'=>'feedback'),'*')
						 ->joinLeft(array('d'=>'detail'),"f.detailid = d.id",array('id as deid','projectname'))
						 ->where('f.detailid = ?',$id);
		$row = $this->_tb->fetchAll($selector);
		$this->_helper->template->head('客户反馈意见详情列表');
		$this->_helper->template->actionMenu(array(
				array('label' => '项目步骤', 'href' => '/pm/step/index/id/'.$id, 'method' => 'detailstep'),
				array('label' => '反馈意见', 'href' => '/pm/feedback/index/id/'.$id, 'method' => 'feedback'),
				array('label' => '反馈意见添加', 'href' => '/pm/feedback/create/id/'.$id, 'method' => 'Createfeedback')));
		if(!empty($row)){
			$row = $row->toArray();
			$this->view->projectname = $row[0]['projectname'];
			$this->view->deid = $row[0]['deid'];
			$this->view->row = $row;	
		}
	}
	
	public function createAction()
	{
		$id = $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()) {
			$datetime = $this->getRequest()->getParam('datetime');
			$proposal = $this->getRequest()->getParam('proposal');
			$arrin = array(
					'detailid' => $id,
					'datetime' => $datetime,
					'proposal' => $proposal
			);
			$this->_tb->insert($arrin);
			$this->_redirect('/pm/feedback/index/id/'.$id);
		}
		$this->view->id=$id;
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$row = $this->_tb->find($id)->current()->toArray();
		if($this->getRequest()->isPost() ) {
	    	$datetime = $this->getRequest()->getParam('datetime');
	    	$proposal = $this->getRequest()->getParam('proposal');
	    	$arrup = array(
	    			'datetime' => $datetime,
	    			'proposal' => $proposal
	    			);
	    	$where = 'id = '.$id;
	    	$this->_tb->update($arrup, $where);
	    	$this->_redirect('/pm/feedback/index/id/'.$row['detailid']);
		}
		$this->view->id= $id;
		$this->view->row = $row;	
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