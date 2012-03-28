<?php
require CONTAINER_PATH.'/app/application/pm/forms/Step/Edit.php';
require CONTAINER_PATH.'/app/application/pm/forms/Step/Distribution.php';
class Pm_StepController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/index/');
		}
		$this->_tb = Class_Base::_('Step');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$detail_sql = $this->_tb->select(false)->setIntegrityCheck(false)
								->from(array('d' => 'detail'),array("d.*","concat(count(distinct(b.id)),'/',count(distinct(a.id))) AS state"))
								->joinLeft(array('a' => 'step'),"d.id = a.detailid",array("count(distinct(a.id))"))
								->joinLeft(array('b' => 'step'),"a.detailid = b.detailid and b.state = 1 ",array("count(distinct(b.id))"))
								->joinLeft(array('u' => 'detail_type'),"d.type = u.id",array('u.type as type'))
								->where('d.id = ?',$id);
		$detailrow = $this->_tb->fetchRow($detail_sql)->toArray();
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('t'=>'step'),array('id','content','percentage','pathurl','starttime','endtime','assess','state'))
						 ->joinLeft(array('u'=>'users_information'),"t.programmer = u.id ", array('username'))
						 ->joinLeft(array('d'=>'detail'),"t.detailid = d.id",array('id as deid','projectname'))
						 ->where('t.detailid = ?',$id);
		$row = $this->_tb->fetchAll($selector)->toArray();
		$tb = Class_Base::_('Skill_Information');
		$sql = $tb->select(false)->from($tb,'*');
		$selrow = $tb->fetchAll($sql)->toArray();
		$this->view->projectname = $detailrow['projectname'];
		$this->view->detailrow = $detailrow;
		$this->view->row = $row;
		$this->view->selrow = $selrow;
		$this->view->deid = $id;
		$this->_helper->template->head('项目步骤详情列表');
		$this->_helper->template->actionMenu(array(
				array('label' => '项目详情管理', 'href' => '/pm/detail/index/', 'method' => 'ManagementDetail'),
				array('label' => '项目添加', 'href' => '/pm/detail/create/', 'method' => 'CreateDetail'),
				array('label' => '反馈意见', 'href' => '/pm/feedback/index/id/'.$id, 'method' => 'CreateDetail')));
	}
	
	public function distributionAction()
	{
		$id = $this->getRequest()->getParam('id');
		$form = new Form_Step_Distribution($id);
		$row = $this->_tb->find($id)->current();
		$arrrow = $row->toArray();
		$html = $form->populate($arrrow);
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$arrone = $form->getValues();
			$where = "id = ".$id;
			$this->_tb->update($arrone, $where);
			$this->_redirect('/pm/step/index/id/'.$arrrow['detailid']);
		}
		$this->view->form = $form;
	}
	
	public function createAction()
	{
		$id = $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()) {
			$val = $this->getRequest()->getParam('val');
			$arrbox = explode(":", $val);
			$selval = $this->getRequest()->getParam('selval');
			$arrone = explode(":", $selval);
			for($i=0;$i<count($arrbox)-1;$i++){
				$arrin = array(
						'detailid' => $id,
						'content' => $arrbox[$i],
						'skillid' => $arrone[$i]
				);
				$step = Class_Base::_('Step');
				$step->insert($arrin);
			}
		}
		$this->view->id=$id;
		exit;
	}
	
	public function editAction()
	{
		$form = new Form_Step_Edit();
		$id = $this->getRequest()->getParam('id');
		$deid = $this->getRequest()->getParam('deid');
		$row = $this->_tb->find($id)->current();
		$arrrow = $row->toArray();
		$html = $form->populate($arrrow);
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
	        $arrone = $form->getValues();
	        $where = "id = ".$id;
	        $this->_tb->update($arrone, $where);
	        $this->_redirect('/pm/step/index/id/'.$deid);
		}
		$this->view->html = $html;
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$this->_redirect('/pm/step/index/id/'.$deid);
	}
}