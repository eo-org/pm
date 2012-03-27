<?php
class Form_Tschedule_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
        $this->addElement('text', 'typestep', array(
            'filters' => array('StringTrim'),
            'label' => '任务步骤：',
            'required' => true
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