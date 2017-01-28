<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace parser;

include_once __DIR__ . DIRECTORY_SEPARATOR . "node.php";

use parser\Node;
use \DOMDocument;
use \DOMXpath;
use \Exception;

class Parser
{
	/** holds DOMDocument instance **/
	private $document;
	
	/** holds DOMXpath instance **/
	private $xPath;

	public function __construct($document)
	{
		if(!class_exists("DOMDocument"))
			throw new Exception("DOMDocument class is not exist");
		$this->loadDocument($document);
	}
	
	public function loadDocument($document)
	{
		if(!file_exists($document))
			throw new Exception("Document file is not exists");
		
		$this->document =  new DOMDocument();
		@$this->document->loadHTMLFile($document);
		@$this->xPath = new DOMXpath($this->document);
	}
	
	public function find($selector, $refNode = NULL)
	{
		$firstChar = substr($selector,0,1);
		$use = "xpath";
		switch ($firstChar) {
			case "#": return $this->findById(substr($selector,1));
					break;

			case ".": return $this->findByClass(substr($selector,1), $refNode);
					break;

			default: return $this->findByXpathQuery($selector, $refNode);
			
		}
	}
		
	public function findById($selector)
	{
		$node = $this->document->getElementById($selector);
		if ($node)
			return new Node($node, $this);
		else
			return false;
	}
		
	public function findByClass($selector, $refNode = NULL)
	{
		$querySelector = '//*[contains(concat(" ", normalize-space(@class), " "), " '.$selector.' ")]';
		return $this->findByXpathQuery($querySelector,$refNode);
	}
		
	public function findByXpathQuery($selector, $refNode = NULL)
	{
		if ( substr($selector,0,2) != "//") {
			$selector = "//".$selector;
		}

		# why it is here I don't remember.
		//if(strlen($selector) > 3)
		//	$selector = "//".$selector;
		
		//echo $selector;
		if($refNode)
			$entries = $this->xPath->query(".".$selector, $refNode);
		else	
			$entries = $this->xPath->query($selector);

		if($entries->length)
			return new Node($entries, $this);
		else 
			return false;
	}
	
	public function html()
	{
		return mb_convert_encoding( $this->document->saveHTML(),"utf-8");
	}
	
	public function getDocument()
	{
		return $this->document;
	}
}

