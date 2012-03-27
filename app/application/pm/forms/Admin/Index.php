<?php
class Form_Admin_Index extends Zend_Form
{
	public function init()
	{
		$this->setName('element_setting');
		 
		$this->addElement('text', 'username', array(
				'filters' => array('StringTrim'),
				'label' => '口令：',
				'required' => true
		));
		$this->addElement('password', 'passwd', array(
				'filters' => array('StringTrim'),
				'label' => '密码：',
				'required' => true
		));
		$this->addElement('submit', 'button', array(
				'filters' => array('StringTrim'),
				'label' => '提交',
		));
		$this->addElement('reset', 'button2', array(
				'filters' => array('StringTrim'),
				'label' => '重置',
		));
	}

}