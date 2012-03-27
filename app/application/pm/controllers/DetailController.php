<?php
class Pm_DetailController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		if(!isset($_SESSION['USERNAME'])){
			$this->_redirect('/pm/admin/');
		}
		$this->_tb = Class_Base::_('Detail');
	}
	public function indexAction()
	{	
		$tb = Class_Base::_('Detail_Type');
		$selector = $tb->select(false)
					   ->from($tb,array('id','type'));
		$type = $tb->fetchAll($selector)->toArray();
		$arrtwo = array();
		foreach ($type as $num => $arrone){
			$arrtwo[$arrone['id']] = $arrone['type'];
		}
		$id = $this->getRequest()->getParam('id');
		if(empty($id)){
			$pathurl = "/pm/detail/get-form-json/";
		}else{
			$pathurl = '/pm/detail/get-form-json/id/'.$id;
		}
		$this->_helper->template->head('项目详情列表');
		$hashParam = $this->getRequest()->getParam('hashParam');
		$labels = array(
				'projectname' => '项目名称',
				'contact' => '联系方式',
				'type' => '项目类型',
				'itemamount' => '项目金额(元)',
				'paid' => '已付款(元)',
				'testurl' => '测试地址',
				'state' => '状态',
				'~contextMenu' => '操作'
		);
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
				'labels' => $labels,
				'selectFields' => array(
						'id' => null,
						'desc' => null,
						'type' => $arrtwo
				),
				'url' => $pathurl,
				'actionId' => 'id',
				'click' => array(
						'action' => 'contextMenu',
						'menuItems' => array(
								array('详情','/pm/step/index/id/')
						)
				),
				'initSelectRun' => 'true',
				'hashParam' => $hashParam
		));
		$this->view->type = $type;
		$this->view->partialHTML = $partialHTML;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目管理', 'href' => '/pm/detail/index/', 'method' => 'ManagementDetail'),
				array('label' => '项目添加', 'href' => '/pm/detail/create/', 'method' => 'CreateDetail')));
	}
	
	public function createAction()
	{
		require CONTAINER_PATH.'/app/application/pm/forms/detail/Edit.php';
		$form = new Form_Detail_Edit();
		$tb = Class_Base::_('Typestep');
		if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$type = $this->getRequest()->getParam('type');
			$detailid = $this->_tb->insert($form->getValues());
			$selector = $tb->select(false)
						   ->from($tb, '*')
						   ->where('typeid = ?',$type);
			$rowset = $this->_tb->fetchAll($selector)->toArray();
			foreach($rowset as $num => $arrone){
				$arrin = array(
						'detailid' => $detailid,
						'content' => $arrone['typestep'],
						'skillid' => $arrone['skillid']
				);
				$step = Class_Base::_('Step');
				$step->insert($arrin);
			}
			$this->_redirect('/pm/detail/index/');
		}
		$this->view->html = $form;
	}
	
	public function addAction()
	{
		$id = $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()) {
			$val = $this->getRequest()->getParam('val');
			$arrbox = explode(":", $val);
			for($i=0;$i<count($arrbox)-1;$i++){
				$arrin = array(
						'detailid' => $id,
						'content' => $arrbox[$i]
				);
				$step = Class_Base::_('Step');
				$step->insert($arrin);
			}
		}
		$this->view->id=$id;
	}
	
	public function getFormJsonAction()
	{
		$pageSize = 20;
		$id = $this->getRequest()->getParam('id');  //29filter_page
		preg_match_all('/\d+/',$id,$arr);	
		if(empty($id)){
			$selector = $this->_tb->select(false)->setIntegrityCheck(false)
								  ->from(array('d' => 'detail'),array("d.*","concat(count(distinct(b.id)),'/',count(distinct(a.id))) AS state"))
								  ->joinLeft(array('a' => 'step'),"d.id = a.detailid",array("count(distinct(a.id))"))
								  ->joinLeft(array('b' => 'step'),"a.detailid = b.detailid and b.state = 1",array("count(distinct(b.id))"))
								  ->group('id');
		}else{
			$selector = $this->_tb->select(false)->setIntegrityCheck(false)
								  ->from(array('d' => 'detail'),array("d.id","d.*","concat(count(distinct(b.id)),'/',count(distinct(a.id))) AS state"))
								  ->joinLeft(array('a' => 'step'),"d.id = a.detailid",array("count(distinct(a.id))"))
								  ->joinLeft(array('b' => 'step'),"a.detailid = b.detailid and b.state = 1 and b.detailid = ".$arr[0][0],array("count(distinct(b.id))"));			  
		}
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
						$selector = $selector->limitPage(intval($value), $pageSize);
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
		require CONTAINER_PATH.'/app/application/pm/forms/detail/Edit.php';
		$form = new Form_Detail_Edit();
		$id = $this->getRequest()->getParam('id');
		$row = $this->_tb->find($id)->current();
		$html = $form->populate($row->toArray());
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
        	$row->setFromArray($html->getValues());
			$row->save();
			$this->_redirect('/pm/detail/index/');
		}
		$this->view->html = $html;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目删除', 'href' => '/pm/detail/del/id/'.$id, 'method' => 'DelDetail')));
	}
	
	public function delAction()
	{
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$schedule = Class_Base::_('Step');
		$where = 'detailid = '.$id;
		$schedule->delete($where);
		$this->_redirect('/pm/detail/index/');
	}
}