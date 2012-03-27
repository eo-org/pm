<?php
class Class_Linklist_Container
{
	private $_headNode = null;
	private $_tailNode = null;
	private $_count = 0;

	public function isEmpty()
	{
		return ($this->_headNode == NULL);
	}

	public function getHeadNode()
	{
		return $this->_headNode;
	}
	
	public function prepend(Class_Linklist_Node $node)
	{
		if($this->_headNode == null) {
			$this->_headNode = $node;
			$this->_tailNode = $node;
		} else {
			$node->setNext($this->_headNode);
			$this->_headNode = $node;
		}
		
		$this->_count++;
	}
//
	public function append(Class_Linklist_Node $node)
	{
		if($this->_tailNode != NULL) {
			echo 'node '.$node->getId().' appended<br />';
			$this->_tailNode->setNext($node);
			$this->_tailNode = $node;
			$this->_count++;
		} else {
			echo 'head '.$node->getId().' appended<br />';
			$this->prepend($node);
			$this->_count++;
		}
	}

//	public function insertBefore($id, $node)
//	{
//		
//	}
//	
	
	
//	public function insert($node)
//	{
//		if($this->_headNode == null) {
//			$this->_headNode = $node;
//			$this->_count++;
//		} else {
//			$preId = $node->preId;
//			$it = $this->getIterator();
//			foreach($it as $k => $n) {
//				if($k == $preId) {
//					$tmp = $n->getNext();
//					$n->setNext($node);
//					$node->setNext($tmp);
//					$this->_count++;
//					return $this;
//				}
//			}
//		}
//		throw new Exception('node '.$node->getId().' does not fit into the container!');
//	}
//	
//	public function deleteHead()
//	{
//		if($this->_headNode != NULL) {
//			$this->_headNode = $this->_headNode->getNext();
//			$this->_count--;
//		}
//		return $this;
//	}
//
//	public function deleteTail()
//	{
//		if($this->_headNode != NULL) {
//			if($this->_headNode->next == NULL)
//			{
//				$this->_headNode = NULL;
//				$this->_count--;
//			}
//			else
//			{
//				$previousNode = $this->_headNode;
//				$currentNode = $this->_headNode->next;
//
//				while($currentNode->next != NULL)
//				{
//					$previousNode = $currentNode;
//					$currentNode = $currentNode->next;
//				}
//
//				$previousNode->next = NULL;
//				$this->_count--;
//			}
//		}
//	}
//
//	public function deleteNode($id)
//	{
//		$current = $this->_headNode;
//		$previous = $this->_headNode;
//
//		while($current->data != $key)
//		{
//			if($current->next == NULL)
//			return NULL;
//			else
//			{
//				$previous = $current;
//				$current = $current->next;
//			}
//		}
//
//		if($current == $this->_headNode)
//		$this->_headNode = $this->_headNode->next;
//		else
//		$previous->next = $current->next;
//
//		$this->_count--;
//	}
//
//	public function find($key)
//	{
//		$current = $this->_headNode;
//		while($current->data != $key)
//		{
//			if($current->next == NULL)
//			return null;
//			else
//			$current = $current->next;
//		}
//		return $current;
//	}

	public function count()
	{
		return $this->_count;
	}
	
	public function getIterator()
	{
		return new Class_Linklist_Iterator($this);
	}
	
	public function printInfo()
	{
		$it = $this->getIterator();
		foreach($it as $k => $node) {
			echo $k.':'.$node->name.'<br />';
		}
	}
}