<?php 
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace parser;

use \DOMNodeList;

class Node implements \iterator
{
	private $parser;

	private $domNode;

	private $nodeType;

	private $currentNode = 0;

	public function __construct($domNode, $parser)
	{
		$this->parser = $parser;
		
		if ($domNode instanceof DOMNodeList ) {
			if ($domNode->length == 1) {
				$this->domNode = $domNode[0];
				$this->nodeType = "single";
			} else {
				foreach ($domNode as $element) {
					$this->domNode[] = $element;
				}
				$this->nodeType = "array";
			}
		} else {
			$this->domNode = $domNode;
			$this->nodeType = "single";
		}
		
	}	
	
	public function __get($key)
	{
		if($this->nodeType == "single")
			return $this->domNode->$key;	
	}
	
	public function __call($method, $args)
	{
		if ($this->nodeType == "array" ) {
			
			foreach ($this->domNode as $node) {
				
				$args1 = $args; 
				$args1[] = $node;
				call_user_func_array(array($this,"_".$method),$args1);
			}
		} else {
			if(method_exists($this,"_".$method)) {
				$args[] = $this->domNode;
				call_user_func_array(array($this,"_".$method),$args);
			} elseif(method_exists($this->domNode,$method)){
				return call_user_func_array(array($this->domNode,$method),$args);	
			}else
				throw new \Exception("no method found with name '$method'");
		}
		
	}
	
	public function getNodeType()
	{
		return $this->nodeType;
	}
	
	
	public function find($selector)
	{	
		if($this->nodeType == "single") {
			return $this->parser->find($selector, $this->domNode);
		}else {
			$results = array();
			foreach($this->domNode as $node) {
				$results[] = $this->parser->find($selector, $node);
			}
			return $results;
		}
	}
	
	public function _addClass($className,$node)
	{
		$class = $node->getAttribute("class");
		if($class)
			$class .= " $className";
		else
			$class = $className;
		$node->setAttribute("class",$class);
	}
	
	public function _removeClass($className,$node)
	{
		$class = $node->getAttribute("class");
		if($class)
		{
			$class = trim($class);
			$class = " ".$class." ";
			$class = str_replace(" ".$className." "," ",$class);
		}
		$node->setAttribute("class",$class);
	}
	
	public function _append($html,$node)
	{
		$node->appendChild($this->getAsNode($html));
	}
	
	public function _prepend($html, $node)
	{	
		//print_r(get_class_methods($node->parentNode));
		if(isset($node->firstChild) and $node->firstChild) {
			$node->insertBefore($this->getAsNode($html), $node->firstChild);
		} else {
			$this->append($html, $node);
		}
	}
	
	
	public function _after($html, $node)
	{
		if(isset($node->nextSibling) and $node->nextSibling) {
			$node->parentNode->insertBefore($this->getAsNode($html), $node->nextSibling);
		} else {
			$this->append($html, $node);
		}
		
	}
	
	public function _before($html, $node)
	{
		$node->parentNode->insertBefore($this->getAsNode($html),$node);		
	}
	
	public function getAsNode($html)
	{
		$fragment = $this->parser->getDocument()->createDocumentFragment();
			
			@$fragment->appendXML( $html);
			if($fragment->hasChildNodes())
				return $fragment;
			else
				throw new Exception("The '$html' html doesn't seem to be valid");
	}
	
	public function _remove($node)
	{
		$node->parentNode->removeChild($node);
	}
	

	/* iterator methods */
	public function current( ) 
	{
		if($this->nodeType =="array")
			return new Node($this->domNode[$this->currentNode],$this->parser);
		else
			return $this;
	}
	
	public function rewind()
	{
		$this->currentNode = 0;
	}
	
	public function valid()
	{
		if($this->nodeType == "array" and isset($this->domNode[$this->currentNode]))
			return true;
		elseif($this->currentNode == 0)
			return true;
		else 
			return false;
				
			
	}
	
	public function key($key)
	{
	if($this->nodeType =="array")
			return new Node($this->domNode[$key],$this->parser);
		else
			return $this;	
	}
	
	public function next()
	{
		$this->currentNode++;
	}

}