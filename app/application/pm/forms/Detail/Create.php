<?php 
class Pm_Forms_Detail_Create extends Zend_Form 
{ 
      public function __construct($options = null) 
      { 
       	   parent::__construct($options);
      }
       
      public function form_text($id,$label)
      {
          $firstName = new Zend_Form_Element_Text($id);
          $text = $firstName->setLabel($label)
          			->setRequired(true)
          			->addValidator('NotEmpty');
          return $text;
      }
      
      public function form_select($id,$label,$arrop)
      {
          	$firstName = new Zend_Form_Element_Select($id);
			$select = $firstName->setLabel($label)
          		  	  ->setMultiOptions($arrop)
          		      ->setRequired(true)
          		      ->addValidator('NotEmpty', true);
			return $select;
      }
      
      public function form_button($label,$type)
      {
          $submit = new Zend_Form_Element_Submit($type);
          $button = $submit->setLabel($label);
          return $button;
      }
      
      public function form_add()
      {
          $this->setName('contact_us');
          $pid = 'pname';
          $plable = '项目名称：';
          $pname = $this->form_text($pid, $plable);
          
          $pid = 'name';
          $plable = '公司名称：';
          $name = $this->form_text($pid, $plable);
          
          $pid = 'kfname';
          $plable = '联系人：';
          $kfname = $this->form_text($pid, $plable);
          
          $pid = 'phone';
          $plable = '联系电话：';
          $phone = $this->form_text($pid, $plable);
          
          $pid = 'type';
          $plable = '项目类型：';
          $arrop = array(
          		'opa' => '网页型',
          		'opb' => 'PHP项目型',
          );
          $type = $this->form_select($pid, $plable, $arrop);
          
          $pid = 'xmname';
          $plable = '项目经理：';
          $xmname = $this->form_text($pid, $plable);
          
          $pid = 'price';
          $plable = '项目价格(元)：';
          $price = $this->form_text($pid, $plable);
          
          $pid = 'sfprice';
          $plable = '已付金额：';
          $sfprice = $this->form_text($pid, $plable);
          
          $plable = '提交';
          $ptype = 'submit';
          $button1 = $this->form_button($plable, $ptype);
          
          $plable = '重置';
          $ptype = 'reset';
          $button2 = $this->form_button($plable, $ptype);
          
          $html = $this->addElements(array($pname, $name, $kfname, $phone, $type, $xmname, $price, $sfprice, $button1,$button2));
          return $html;
      }
}
?>

