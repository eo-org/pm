<?php
class Project_ScheduleController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/project/admin/');
		}
		$this->_tb = Class_Base::_('Schedule');
	}
	public function indexAction()
	{	
		$id = $this->getRequest()->getParam('id');		
		$this->_helper->template->head('项目详情列表');
		$hashParam = $this->getRequest()->getParam('hashParam');
		$labels = array(
				'content' => '任务内容',
				'programmer' => '完成人员',
				'percentage' => '完成进度(%)',
				'pathurl' => '文件上传路径',
				'assess' => '经理点评',
				'~contextMenu' => '操作'
		);
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
				'labels' => $labels,
				'selectFields' => array(
						'id' => null,
						'desc' => null
				),
				'url' => '/project/schedule/get-form-json/id/'.$id.'/',
				'actionId' => 'id',
				'click' => array(
						'action' => 'contextMenu',
						'menuItems' => array(
								array('分发任务', '/project/schedule/distribution/id/'),
								array('编辑', '/project/schedule/edit/id/'),
								array('删除', '/project/schedule/del/deid/'.$id.'/id/')
						)
				),
				'initSelectRun' => 'true',
				'hashParam' => $hashParam
		));
		
		$this->view->partialHTML = $partialHTML;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目详情管理', 'href' => '/project/detail/index/', 'method' => 'ManagementDetail'),
				array('label' => '项目添加', 'href' => '/project/detail/create/', 'method' => 'CreateDetail'),
				array('label' => '项目类型管理', 'href' => '/project/type/index/', 'method' => 'ManagementType'),
				array('label' => '类型添加', 'href' => '/project/type/create/', 'method' => 'CreateType')));
// 		$this->_helper->template->actionMenu(array(
// 				array('label' => '创建新项目', 'href' => '/project/detail/create/', 'method' => 'createElementSetting')));
	}
	
	public function createAction()
	{
		require CONTAINER_PATH.'/app/application/project/forms/detail/Edit.php';
		$form = new Form_Detail_Edit();
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$this->_tb->insert($form->getValues());
			$this->_redirect('/project/detail/index/');
		}
		$this->view->html = $form;
	}
	
	public function getFormJsonAction()
	{
		$id = $this->getRequest()->getParam('id');
		$tb = Class_Base::_('Detail');
		$selector = $tb->find($id)->current();
		$row = $selector->toArray();
		$pageSize = 10;
		$step = Class_Base::_('Step');
		$selector = $step->select(false)->setIntegrityCheck(false)
						 ->from(array('t'=>'step'),array('content'))
						 ->joinLeft(array('s'=>'schedule'),"s.stepid = t.id and s.detailid = ".$id, array('id','programmer','percentage','pathurl','assess'))
						 ->where('typeid = ?',$row['type']);
		$rowset = $step->fetchAll($selector)->toArray();
// 		Zend_Debug::dump($rowset);
// 		exit;
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
							$value = 1;
						}
						$selector->limitPage(intval($value), $pageSize);
						$result['currentPage'] = intval($value);
						break;
				}
			}
		}
	
		$rowset = $this->_tb->fetchAll($selector)->toArray();
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
		require CONTAINER_PATH.'/app/application/project/forms/schedule/Edit.php';
		$form = new Form_Schedule_Edit();
		$id = $this->getRequest()->getParam('id');
		$row = $this->_tb->find($id)->current();
		$arrrow = $row->toArray();
		$html = $form->populate($arrrow);
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
	        $row->setFromArray($html->getValues());
			$row->save();
			$this->_redirect('/project/schedule/index/id/'.$arrrow['detailid']);
		}
		$this->view->html = $html;
	}
	
	public function delAction()
	{
		$deid = $this->getRequest()->getParam('deid');
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$arr = array(
					'programmer' => '',
					'percentage' =>0,
					'pathurl' => '',
					'assess' => ''
				);
		$row = $this->_tb->update($arr, $where);
		$this->_redirect('/project/schedule/index/id/'.$deid);
	}
}