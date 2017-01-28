<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace UniqueValidator;

use UniqueValidator\Validator;
use UniqueValidator\FormParser;
use UniqueValidator\Config;
use UniqueValidator\Cache;

use parser\Parser;

use \Exception;


/**
 *	UValidator : it's facade class which is used to serve the uniquevalidator to the client code .
 */
class UValidator 
{
	private $config;
	
	private $formParser;
	
	private $validator;

	private $errors;
	
	private $cacheDisabled;
	public function __construct()
	{
		$this->config = new Config();		
		$this->cache = new Cache();	
		$this->cache->setCacheDir("../cache");
		$this->cacheDisabled = true;
		$this->validator = new Validator($this->config); 
		#TODO:: add the html5 attributes and types array in config.
	}
		
	public function validate($formUrl,$customMessages = null)
	{
		if(!strstr($formUrl, "#"))
			throw new Exception("Form ID not specified at $formUrl the input should be form_full_path#formId");
		
		$form = explode("#",$formUrl);
		
		# extract form id and form file 
		$formFile = $form[0];
		$formId = $form[1];
		
		if( $this->cacheDisabled or !($this->formParser = $this->cache->get($formUrl) )) {
			
			$parser = new Parser($formFile);
			
			$this->formParser = new FormParser($parser,$formId);				
			$parsedArray = $this->formParser->parse($formFile);
			try {
				$this->cache->set($formUrl, $this->formParser);
			}catch(Exception $e){
				die("ERROR: ".$e->getMessage());	
			}
		} else {	
			$parsedArray =  $this->formParser->getParsedArray();
		}
		
		$submittedValues = $this->getSubmittedValues();
		
		$this->validator->setParsedArray($parsedArray);
		$this->validator->setSubmittedValues($submittedValues);
		$this->validator->setValidatorsLocation(__DIR__ . "/../../validators");
		
		if($customMessages)
			$this->validator->setCustomErrorMessages($customMessages);
		
		return $this->validator->validate();
					
	}
	
	
	public function getErrors($attributeNameAsKey = false)
	{
		if($this->errors and count($errors) >= 1)
			return $this->errors;
		
		if($attributeNameAsKey)
			$parsedArray = $this->formParser->getParsedArray();
		
		$results = $this->validator->getValidationResults();
		foreach($results as $field =>$result) {
			
			if($attributeNameAsKey)
				$field = $parsedArray[$field]['name'];
			
			if(is_array($result)) {
				foreach($result as $index => $res) {
					if($res !== 1)
					$this->errors[$field][$index] = $res;
				}
			} else {
				if($result !== 1 )
					$this->errors[$field] = $result;
			}
		}
		
		if($this->errors and count($this->errors)>= 1)
			return $this->errors;
		
		
	}
	
	
	public function getSubmittedValues()
	{
		if(strtoupper($this->formParser->getFormMethod()) == "POST") {
			return $_POST;
		} else {
			return $_GET;
		}
	}

	public function setCallbackForField($field, $callback)
	{
		$this->validator->setCallbackForField($field, $callback);
	}
}