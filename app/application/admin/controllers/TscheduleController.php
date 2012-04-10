<?php
class Admin_TscheduleController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/admin/index/');
		}
		$this->_tb = Class_Base::_('Typestep');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');		
		$selector = $this->_tb->select(false)->setIntegrityCheck(false)
						      ->from(array('s' => 'typestep'), '*')
						      ->joinLeft(array('t' => 'detail_type'),"s.typeId=t.id",array('type'))
						      ->joinLeft(array('i' => 'skill_information'),"s.skillId = i.id",array('skillName'))
							  ->where('typeId = ?',$id);
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
				array('label' => '项目类型管理', 'href' => '/pm/type/index/', 'method' => 'ManagementDetail'),
				array('label' => '类型添加', 'href' => '/pm/type/create/', 'method' => 'ManagementDetail')));
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
						'typeId' => $id,
						'typeStep' => $arrbox[$i],
						'skillId' => $arrone[$i]
				);
				$this->_tb->insert($arrin);
			}
		}
		$this->view->id=$id;
	}
	
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$typestep = $this->getRequest()->getParam('typeStep');
		$skillid = $this->getRequest()->getParam('skillId');
		$arrin = array(
				'typeStep' => $typestep,
				'skillId' => $skillid
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
						      ->joinLeft(array('t' => 'detail_type'),"s.typeId=t.id",array('type'))
						      ->joinLeft(array('i' => 'skill_information'),"s.skillId = i.id",array('skillName'))
							  ->where('s.id = ?',$id);
		$row = $this->_tb->fetchRow($selector)->toArray();	
		$html = "<td id='typestep'>".$row['typestep']."</td><td class='".$row['skillId']."' id='skillname'>".$row['skillName']."</td>";
		$html.= "<td><a class='".$row['id']."' href='#' id='editstep'>修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='/pm/tschedule/del/deid/".$deid."/id/".$row['id']."'>删除</a></td>";
		echo $html;
		exit;	
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$this->_redirect('/pm/tschedule/index/id/'.$deid);
	}
}