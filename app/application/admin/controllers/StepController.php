<?php
require CONTAINER_PATH.'/app/application/admin/forms/Step/Edit.php';
require CONTAINER_PATH.'/app/application/admin/forms/Step/Distribution.php';
class Admin_StepController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		$this->_tb = Class_Base::_('Step');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$detail_sql = $this->_tb->select(false)->setIntegrityCheck(false)
								->from(array('d' => 'detail'),array("d.*","concat(count(distinct(b.id)),'/',count(distinct(a.id))) AS state"))
								->joinLeft(array('a' => 'step'),"d.id = a.detailId",array("count(distinct(a.id))"))
								->joinLeft(array('b' => 'step'),"a.detailId = b.detailId and b.state = 1 ",array("count(distinct(b.id))"))
								->joinLeft(array('u' => 'detail_type'),"d.type = u.id",array('u.type as type'))
								->where('d.id = ?',$id);
		$detailrow = $this->_tb->fetchRow($detail_sql)->toArray();
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						 ->from(array('t'=>'step'),array('id','content','percentage','pathurl','startTime','endTime','assess','state'))
						 ->joinLeft(array('u'=>'users_information'),"t.programmer = u.id ", array('userName'))
						 ->joinLeft(array('e' => 'entrust'),"t.id = e.stepId and e.state = 1",array('e.id as entrustid','e.userId'))
						 ->joinLeft(array('i' => 'users_information'),"e.userId = i.id",array('i.userName as entrustname'))
						 ->where('t.detailId = ?',$id);
		$row = $this->_tb->fetchAll($selector)->toArray();
		$tb = Class_Base::_('Skill_Information');
		$sql = $tb->select(false)->from($tb,'*');
		$selrow = $tb->fetchAll($sql)->toArray();
		$this->view->projectname = $detailrow['projectName'];
		$this->view->detailrow = $detailrow;
		$this->view->row = $row;
		$this->view->selrow = $selrow;
		$this->view->deid = $id;
		$this->_helper->template->head('项目步骤详情列表');
		$this->_helper->template->actionMenu(array(
				array('label' => '项目详情管理', 'href' => '/admin/detail/index/', 'method' => 'ManagementDetail'),
				array('label' => '项目添加', 'href' => '/admin/detail/create/', 'method' => 'CreateDetail'),
				array('label' => '反馈意见', 'href' => '/admin/feedback/index/id/'.$id, 'method' => 'CreateDetail')));
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
			$this->_redirect('/admin/step/index/id/'.$arrrow['detailid']);
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
						'detailId' => $id,
						'content' => $arrbox[$i],
						'skillId' => $arrone[$i]
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
	        $this->_redirect('/admin/step/index/id/'.$deid);
		}
		$this->view->html = $html;
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$tb = Class_Base::_('Entrust');
		$where = "stepId = ".$id;
		$tb->delete($where);
		$this->_redirect('/admin/step/index/id/'.$deid);
	}
}