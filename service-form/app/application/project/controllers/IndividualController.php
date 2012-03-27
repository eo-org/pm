<?php
class Project_IndividualController extends Zend_Controller_Action
{
	private $_schedule;
	private $_Digits;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/project/admin/');
		}
		$this->_Digits = new Zend_Filter_Digits();
	}
	public function indexAction()
	{	
		$sid = $this->_Digits->filter($this->getRequest()->getParam('sid'),0);
		if($sid == 0){
			$this->_helper->template->head('未完成任务列表');
		}else{
			$this->_helper->template->head('完成任务列表');
		}
		$tb = Class_Base::_('Step');
		$selector = $tb->select(false)->setIntegrityCheck(false)
					   ->from(array('s' => 'step'), '*')
					   ->joinLeft(array('d' => 'detail'),"s.detailid = d.id", array('projectname'))
					   ->where('s.programmer = ?',$_SESSION['USERID'])
					   ->where('s.state = ?',$sid);
		$rowset = $tb->fetchAll($selector)->toArray();
		$this->view->sid = $sid;
		$this->view->rowset = $rowset;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/project/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/project/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/project/individual/sel/', 'method' => 'CreateDetail')));
	}
	
	public function selAction()
	{
		$this->_helper->template->head('完成任务列表');
		$tb = Class_Base::_('Users_Information');
		$id = $_SESSION['USERID'];
		$row = $tb->find($id)->current()->toArray();
		$tb_skill = Class_Base::_('Skill');
		$sql = $tb_skill->select(false)->setIntegrityCheck(false)
						->from(array('s' => 'skill'),array('skillid'))
						->joinLeft(array('i' => 'skill_information'),"s.skillid = i.id",array('skillname'))
						->where('userid = ?',$id);
		$rowstep = $tb_skill->fetchAll($sql)->toArray();
		$tb_information = Class_Base::_('Skill_Information');
		$selsql = $tb_information->select(false)
								 ->from($tb_information,'*');
		$this->view->skillinformation = $tb_information->fetchAll($selsql)->toArray();
		$this->view->row = $row;
		$this->view->rowset = $rowstep;
		$this->_helper->template->actionMenu(array(
				array('label' => '未完成任务', 'href' => '/project/individual/index/sid/0', 'method' => 'ManagementDetail'),
				array('label' => '完成任务', 'href' => '/project/individual/index/sid/1', 'method' => 'ManagementDetail'),
				array('label' => '个人信息', 'href' => '/project/individual/sel/', 'method' => 'CreateDetail')));
	}
	
	public function getFormJsonAction()
	{
		$sid = $this->_Digits->filter($this->getRequest()->getParam('sid'),0);
		$pageSize = 10;
		$tb = Class_Base::_('Step');
		$selector = $tb->select(false)
					   ->from($tb, '*')
					   ->where('programmer = ?',$_SESSION['USERID'])
					   ->where('state = ?',$sid)
					   ->order('starttime DESC')
					   ->limitPage(1, $pageSize);
		$result = array();
		foreach($this->getRequest()->getParams() as $key => $value) {
			if(substr($key, 0 , 7) == 'filter_') {
				$field = substr($key, 7);
				switch($field) {
					case 'type':
						$selector->where('type like ?', '%'.$value.'%');
						break;
					case 'state':
						$selector->where('state like ?', '%'.$value.'%');
						break;
					case 'label':
						$selector->where('label like ?', '%'.$value.'%');
						break;
					case 'page':
						if(intval($value) == 0) {
							$value = 2;
						}
						$selector = $selector->limitPage(intval($value), $pageSize);
						$result['currentPage'] = intval($value);
						break;
				}
			}
		}
		$rowset = $tb->fetchAll($selector)->toArray();
		$result['data'] = $rowset;
		$result['dataSize'] = App_Func::count($selector);
		$result['pageSize'] = $pageSize;
	
		if(empty($result['currentPage'])) {
			$result['currentPage'] = 1;
		}
		return $this->_helper->json($result);
	}
	
	public function editAction()
	{
		require CONTAINER_PATH.'/app/application/project/forms/individual/Edit.php';
		$form = new Form_Individual_Edit();
		$id = $this->getRequest()->getParam('id');
		$tb = Class_Base::_('Step');
		$row = $tb->find($id)->current();
		$html = $form->populate($row->toArray());
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
        	$row->setFromArray($html->getValues());
			$row->save();
			$this->_redirect('/project/individual/index/');
		}
		$this->view->html = $html;
	}
	
	public function edituserAction()
	{
		$username = $this->getRequest()->getParam('username');
		$sex = $this->getRequest()->getParam('sex');
		$age = $this->getRequest()->getParam('age');
		$QQ = $this->getRequest()->getParam('QQ');
		$phone = $this->getRequest()->getParam('phone');
		$skillid = $this->getRequest()->getParam('skillid');
		$arrup = array(
				'username' => $username,
				'sex' => $sex,
				'age' => $age,
				'QQ' => $QQ,
				'phone' => $phone
			);
		$tb = Class_Base::_('Users_Information');
		$id = $_SESSION['USERID'];
		$where = 'id = '.$id;
		$tb->update($arrup, $where);
		$arrbox = explode(":", $skillid);
		$skill = Class_Base::_('Skill');
		$where = 'userid = '.$id;
		$skill->delete($where);
		for($i=0;$i<count($arrbox)-1;$i++){
			$arrin = array(
					'userid' => $id,
					'skillid' => $arrbox[$i]
			);	
			$skill->insert($arrin);
		}
	}
}