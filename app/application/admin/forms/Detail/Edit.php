<?php
class Form_Detail_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
    	$this->addElement('text', 'detailTime', array(
    			'filters' => array('StringTrim'),
    			'label' => '时间：',
    			'required' => true
    	));
        $this->addElement('text', 'projectName', array(
            'filters' => array('StringTrim'),
            'label' => '项目名称：',
            'required' => true
        ));
       $this->addElement('text', 'contact', array(
            'filters' => array('StringTrim'),
            'label' => '联系方式：',
            'required' => true
        ));
       $db = Class_Base::_('Detail_Type');
       $selector = $db->select(false)
       				  ->from($db,array('id','type'));
       $type = $db->fetchAll($selector)->toArray();
       foreach ($type as $num => $arrone){
       		$arrtwo[$arrone['id']] = $arrone['type'];
       }
       $this->addElement('select', 'type', array(
       		'label' => '项目类型：',
       		'required' => true,
       		'multiOptions' => $arrtwo
       ));
       $this->addElement('text', 'itemamount', array(
       		'filters' => array('StringTrim'),
       		'label' => '项目价格：'
       ));
       $this->addElement('text', 'paid', array(
       		'filters' => array('StringTrim'),
       		'label' => '已付金额：'
       ));
       $this->addElement('text', 'testUrl', array(
       		'filters' => array('StringTrim'),
       		'label' => '测试地址：'
       ));
       $this->addElement('text', 'domain', array(
       		'filters' => array('StringTrim'),
       		'label' => '域名：'
       ));
       $this->addElement('submit', 'button', array(
       		'filters' => array('StringTrim'),
       		'label' => '提交',
       ));
       $this->addElement('reset', 'button2', array(
       		'filters' => array('StringTrim'),
       		'label' => '重置',
       ));
       $this->addElement('button', 'bt_return', array(
       		'filters' => array('StringTrim'),
       		'label' => '返回',
       ));
    }
 
}