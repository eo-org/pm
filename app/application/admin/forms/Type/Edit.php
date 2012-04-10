<?php
class Form_Type_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
        $this->addElement('text', 'type', array(
            'filters' => array('StringTrim'),
            'label' => '类型名称：',
            'required' => true
        ));
        $this->addElement('text', 'createName', array(
            'filters' => array('StringTrim'),
            'label' => '创建人：',
            'required' => true
        ));
       $this->addElement('submit', 'button', array(
       		'filters' => array('StringTrim'),
       		'label' => '提交'
       ));
       $this->addElement('reset', 'button2', array(
       		'filters' => array('StringTrim'),
       		'label' => '重置'
       ));
       $this->addElement('button', 'bt_return', array(
       		'filters' => array('StringTrim'),
       		'label' => '返回',
       ));
    }
}