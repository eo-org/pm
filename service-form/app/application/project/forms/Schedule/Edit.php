<?php
class Form_Schedule_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');

        $this->addElement('text', 'programmer', array(
            'filters' => array('StringTrim'),
            'label' => '完成人员：',
            'required' => true
        ));
       $this->addElement('text', 'percentage', array(
            'filters' => array('StringTrim'),
            'label' => '完成进度(%)：',
            'required' => true
        ));
       $this->addElement('text', 'pathurl', array(
       		'filters' => array('StringTrim'),
       		'label' => '文件上传路径：'
       ));
       $this->addElement('text', 'assess', array(
       		'filters' => array('StringTrim'),
       		'label' => '经理点评：'
       ));
       $this->addElement('submit', 'button', array(
       		'filters' => array('StringTrim'),
       		'label' => '提交',
       ));
       $this->addElement('button', 'bt_return', array(
       		'filters' => array('StringTrim'),
       		'label' => '返回',
       ));
    }
 
}