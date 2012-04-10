<?php
class Form_Users_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
        $this->addElement('text', 'userName', array(
            'filters' => array('StringTrim'),
            'label' => '用户名：',
            'required' => true
        ));
        $arr = array(
        	'1' => '男',
        	'0' => '女'	
        );
        $this->addElement('select', 'sex', array(
            'filters' => array('StringTrim'),
            'label' => '性别：',
            'multiOptions' => $arr
        ));  
       $arrone = array(
       		'20以下' => '20以下', 	
       		'20~30' => '20~30',
       		'30~40' => '30~40',
       		'40以上' => '40以上',
       		'保密' => '保密'
        );
       $this->addElement('select', 'age', array(
       		'label' => '年龄：',
       		'required' => true,
       		'multiOptions' => $arrone
       ));
       $this->addElement('text', 'QQ', array(
       		'filters' => array('StringTrim'),
       		'label' => 'QQ：'
       ));
       $this->addElement('text', 'phone', array(
       		'filters' => array('StringTrim'),
       		'label' => '联系电话：'
       ));
       
       $tb = Class_Base::_('Skill_Information');
       $sql = $tb->select(false)
       			 ->from($tb,'*');
       $row = $tb->fetchAll($sql)->toArray();
       $arrtwo = array();
       foreach ($row as $num => $arrone){
       		$arrtwo[$arrone['id']] = $arrone['skillName'];
       }
       $this->addElement('MultiCheckbox', 'skill', array(
       		'label' => '技能：',
       		'MultiOptions' => $arrtwo,
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
       $this->addElement('button', 'bt_return', array(
       		'filters' => array('StringTrim'),
       		'label' => '返回',
       ));
    }
 
}