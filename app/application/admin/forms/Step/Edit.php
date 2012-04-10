<?php
class Form_Step_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
        $this->addElement('text', 'content', array(
            'filters' => array('StringTrim'),
            'label' => '任务内容：',
            'required' => true
        ));
        $db = Class_Base::_('Skill_Information');
        $selector = $db->select(false)
        			   ->from($db,'*');
        $type = $db->fetchAll($selector)->toArray();
        foreach ($type as $num => $arrone){
        	$arrtwo[$arrone['id']] = $arrone['skillName'];
        }
        $this->addElement('select', 'skillId', array(
        		'label' => '处理部门：',
        		'required' => true,
        		'multiOptions' => $arrtwo
        ));
        $this->addElement('textarea', 'assess', array(
            'filters' => array('StringTrim'),
            'label' => '点评：'
        ));
        $arrone = array(
        			'0' => '进行中',
        			'1' => '完成'
        		);
      	$this->addElement('select', 'state', array(
            'filters' => array('StringTrim'),
            'label' => '完成状态：',
        	'multiOptions' => $arrone
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