<?php
class Form_Step_Distribution extends Zend_Form
{
	private $_id;
	public function __construct($id)
	{
		
		$this->_id = $id;
		parent::__construct();
	}
	
    public function init()
    {
    	$this->setName('element_setting');
    	
    	$db = Class_Base::_('Users_Information');
    	$selector = $db->select(false)->setIntegrityCheck(false)
					   ->from(array('t' => 'step'),array('id as deid'))
					   ->joinLeft(array('s' => 'skill'),"t.skillId = s.skillId",array("skillId"))
					   ->joinLeft(array('u' => 'users_information'),"u.id = s.userId",array("id","userName"))
					   ->where('t.id = ?',$this->_id);
    	 
    	$type = $db->fetchAll($selector)->toArray();
    	foreach ($type as $num => $arrone){
    		$arrtwo[$arrone['id']] = $arrone['userName'];
    	}
    	
        $this->addElement('select', 'programmer', array(
            'filters' => array('StringTrim'),
            'label' => '完成人员：',
        	'multiOptions' => $arrtwo
        ));
        $this->addElement('text', 'startTime', array(
            'filters' => array('StringTrim'),
            'label' => '开始时间：' 
        ));
       $this->addElement('text', 'endTime', array(
            'filters' => array('StringTrim'),
            'label' => '结束时间：'
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