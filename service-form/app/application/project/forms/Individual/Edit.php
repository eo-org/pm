<?php
class Form_Individual_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
        $this->addElement('text', 'percentage', array(
            'filters' => array('StringTrim'),
            'label' => '完成进度：',
            'required' => true
        ));
        $this->addElement('file', 'pathurl', array(
            'filters' => array('StringTrim'),
            'label' => '文件上传：'
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