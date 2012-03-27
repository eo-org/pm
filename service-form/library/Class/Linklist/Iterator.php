<?php
class Class_Linklist_Iterator implements Iterator
{
	/**
	 * 
	 * @var Class_Linklist_Node
	 */
	protected $_current;
	protected $_clc;
	
    public function __construct(Class_Linklist_Container $clc)
    {
    	$this->_clc = $clc;
    }

    function rewind()
    {
        $this->_current = $this->_clc->getHeadNode();
    }

    function current()
    {
        return $this->_current;
    }

    function key()
    {
        return $this->_current->getId();
    }

    function next()
    {
        $this->_current = $this->_current->getNext();
    }

    function valid()
    {
        return $this->_current == null ? false : true;
    }
}