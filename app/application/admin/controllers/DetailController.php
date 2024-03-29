<?php
require CONTAINER_PATH.'/app/application/admin/forms/Detail/Edit.php';
class Admin_DetailController extends Zend_Controller_Action
{
	private $_tb;
	public function init()
	{
		//$csu = Class_Session_User::getInstance();
		//$csu->getUserId();
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
			$pathurl = "/admin/detail/get-form-json/";
		}else{
			$pathurl = '/admin/detail/get-form-json/id/'.$id;
		}
		$this->_helper->template->head('项目详情列表');
		$hashParam = $this->getRequest()->getParam('hashParam');
		$labels = array(
				'detailTime' => '时间',
				'projectName' => '项目名称',
				'contact' => '联系方式',
				'type' => '项目类型',
				'itemamount' => '项目金额(元)',
				'paid' => '已付款(元)',
				'testUrl' => '测试地址',
				'domain' => '域名',
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
								array('详情','/admin/step/index/id/')
						)
				),
				'initSelectRun' => 'true',
				'hashParam' => $hashParam
		));
		$this->view->type = $type;
		$this->view->partialHTML = $partialHTML;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目添加', 'href' => '/admin/detail/create/', 'method' => 'CreateDetail')));
	}
	
	public function createAction()
	{
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
						'detailId' => $detailid,
						'content' => $arrone['typeStep'],
						'skillId' => $arrone['skillId']
				);
				$step = Class_Base::_('Step');
				$step->insert($arrin);
			}
			$this->_redirect('/admin/detail/index/');
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
						'detailId' => $id,
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
								  ->joinLeft(array('a' => 'step'),"d.id = a.detailId",array("count(distinct(a.id))"))
								  ->joinLeft(array('b' => 'step'),"a.detailId = b.detailId and b.state = 1",array("count(distinct(b.id))"))
								  ->limitPage(1, $pageSize)
								  ->order('d.id desc')
								  ->group('id');
		}else{
			$selector = $this->_tb->select(false)->setIntegrityCheck(false)
								  ->from(array('d' => 'detail'),array("d.*","concat(count(distinct(b.id)),'/',count(distinct(a.id))) AS state"))
								  ->joinLeft(array('a' => 'step'),"d.id = a.detailId",array("count(distinct(a.id))"))
								  ->joinLeft(array('b' => 'step'),"a.detailId = b.detailId and b.state = 1 ",array("count(distinct(b.id))"))
								  ->where('d.id = ?',$arr[0][0])
								  ->order('d.id desc');			  
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
		//$result['dataSize'] = App_Func::count($selector);
		$result['pageSize'] = $pageSize;
	
		if(empty($result['currentPage'])) {
			$result['currentPage'] = 1;
		}
		return $this->_helper->json($result);
	}
	
	public function editAction()
	{
		$form = new Form_Detail_Edit();
		$id = $this->getRequest()->getParam('id');
		$row = $this->_tb->find($id)->current();
		$html = $form->populate($row->toArray());
		if($this->getRequest()->isPost() && $html->isValid($this->getRequest()->getParams()) ) {
        	$row->setFromArray($html->getValues());
			$row->save();
			$this->_redirect('/admin/detail/index/');
		}
		$this->view->html = $html;
		$this->_helper->template->actionMenu(array(
				array('label' => '项目删除', 'href' => '/admin/detail/del/id/'.$id, 'method' => 'DelDetail')));
	}
	
	public function delAction()
	{
		$id = $this->getRequest()->getParam('id');
		$where = 'id = '.$id;
		$row = $this->_tb->delete($where);
		$schedule = Class_Base::_('Step');
		$where = 'detailId = '.$id;
		$schedule->delete($where);
		$this->_redirect('/admin/detail/index/');
	}
}