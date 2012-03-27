<?php
class Project_TscheduleController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/project/admin/');
		}
		$this->_tb = Class_Base::_('Typestep');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');		
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						      ->from(array('s' => 'typestep'), '*')
						      ->joinLeft(array('t' => 'detail_type'),"s.typeid=t.id",array('type'))
						      ->joinLeft(array('i' => 'skill_information'),"s.skillid = i.id",array('skillname'))
							  ->where('typeid = ?',$id);
		$rowset = $this->_tb->fetchAll($selector)->toArray();
		$tb = Class_Base::_('Skill_Information');
		$sql = $tb->select(false)
				  ->from($tb,'*');
		$row = $tb->fetchAll($sql)->toArray();
		$this->_helper->template->head('类型步骤列表');
		if(!empty($rowset)){
			$this->view->typename = $rowset[0]['type'];
		}
		$this->view->rowset = $rowset;
		$this->view->row = $row;
		$this->view->deid = $id;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目类型管理', 'href' => '/project/type/index/', 'method' => 'ManagementDetail'),
				array('label' => '类型添加', 'href' => '/project/type/create/', 'method' => 'ManagementDetail')));
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
						'typeid' => $id,
						'typestep' => $arrbox[$i],
						'skillid' => $arrone[$i]
				);
				$this->_tb->insert($arrin);
			}
		}
		$this->view->id=$id;
	}
	
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$typestep = $this->getRequest()->getParam('typestep');
		$skillid = $this->getRequest()->getParam('skillid');
		$arrin = array(
				'typestep' => $typestep,
				'skillid' => $skillid
				);
		$where = "id = ".$id;
		$this->_tb->update($arrin, $where);
	}
	
	public function selAction()
	{
		$id = $this->getRequest()->getParam('id');
		$deid = $this->getRequest()->getParam('deid');
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						      ->from(array('s' => 'typestep'), '*')
						      ->joinLeft(array('t' => 'detail_type'),"s.typeid=t.id",array('type'))
						      ->joinLeft(array('i' => 'skill_information'),"s.skillid = i.id",array('skillname'))
							  ->where('s.id = ?',$id);
		$row = $this->_tb->fetchRow($selector)->toArray();	
		$html = "<td id='typestep'>".$row['typestep']."</td><td class='".$row['skillid']."' id='skillname'>".$row['skillname']."</td>";
		$html.= "<td><a class='".$row['id']."' href='#' id='editstep'>修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='/project/tschedule/del/deid/".$deid."/id/".$row['id']."'>删除</a></td>";
		echo $html;
		exit;	
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$this->_redirect('/project/tschedule/index/id/'.$deid);
	}
}