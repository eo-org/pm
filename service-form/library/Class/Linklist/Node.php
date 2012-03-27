<?php
interface Class_Linklist_Node
{
	public function getId();
	
	public function setNext(Class_Linklist_Node $node);
	
	public function getNext();
	
	public function setPre(Class_Linklist_Node $node);
	
	public function getPre();
}