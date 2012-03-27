<?php
abstract class Project_Base_BaseController extends Zend_Controller_Action
{
    public function init()
    {
        if(!isset($_SESSION['ADMIN'])){
            $this->_redirect('/project/admin/htdl/');
        }
    }
}