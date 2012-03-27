<?php
class Class_Model_Detail_Row extends Zend_Db_Table_Row_Abstract 
{
	public function getEditForm()
	{
		require_once APP_PATH."/project/forms/Detail/Edit.php";
		$form = new Form_Detail_Edit();
		$form->populate(parent::toArray());
		return $form;
	}
}