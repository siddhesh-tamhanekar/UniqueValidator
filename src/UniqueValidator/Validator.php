<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace UniqueValidator;

use UniqueValidator\AbstractValidator;
use \Exception;

include_once __DIR__ ."/AbstractValidator.php";

/**
 *	Validator : validates the data and generate results .
 */
class Validator 
{
	private $parsedArray = array();
		
	private $submittedValues	 = array();
	
	private $validationResult = array();
	
	private $customErrorMessages = array();	
	
	private $callbacks = array();

	private $config = null;
	
	private $logger = null;
	
	private $validationStatus = true;
	
	private $currentValidatingField = null;
	
	public function __construct($config = null )
	{
		$this->config = $config;
	}

	public function setParsedArray($parsedArray)
	{
		$this->parsedArray = $parsedArray;
	}

	public function setSubmittedValues($submittedValues)
	{
		$this->submittedValues = $submittedValues;
	}

	public function setCustomErrorMessages($errorMessages)
	{
		$this->customErrorMessages = $errorMessages;
	}
	

	public function setValidatorsLocation($location)
	{
		if(is_dir($location)) {
			
			$this->validatorsLocation = rtrim($location,"/\\");
			# adding bunch of well known validators at once so includes  will be reduced
			include_once $this->validatorsLocation ."/html5_validators.php";
		}else {
			throw new Exception("'$location' folder does not Exists");
		}
		
	}
	
	public function setCallbackForField($field,$callback)
	{
		if(is_callable($callback))
			$this->callbacks[$field][] = $callback;
	}
	
	public function getValidationResults()
	{
		return $this->validationResult;
	}
	
	private function isInputGroup()
	{
		return  isset($this->currentValidatingField['fieldType']) and $this->currentValidatingField['fieldType'] == "group";
	}
	
	private function getFieldSubmittedValue($key)
	{
		if(isset($this->submittedValues[$key])) {
			return $this->submittedValues[$key];
		} else {
			return ($this->currentValidatingField['expectedValue'] == "array")? array() : "";	
			
		}   
	}
	
	
	public function validate()
	{
		foreach($this->parsedArray as $key => $field) {
			
			$this->currentValidatingField = $field;

			$submittedValues = $this->getFieldSubmittedValue($key);
			
			if($this->isInputGroup()) {
				
				$result = $this->applyGroupValidator($field,$submittedValues);
				$this->validationResult[$key] = $result;
				if($result !== 1) {
					$this->validationStatus = false;
					continue;
				}
				
				$this->validationResult[$key] = array();				
				
				foreach($submittedValues as $i =>$eachValue)
				{
					if(!$eachValue)
						continue;
					
					$result = $this->validateField($field, $eachValue);
					$this->validationResult[$key][$i] = $result;
					
					if($result !== 1)
						$this->validationStatus = false;	
				}
				
			} else {
				
				$result = $this->validateField($field, $submittedValues);
				# store result.
				$this->validationResult[$key] = $result;
				if($result !== 1)
					$this->validationStatus = false;				
			}
		}
		return $this->validationStatus;
	}
	
	private function applyGroupValidator($field, $value)
	{
		$field['user_value'] = $value;
		$message = $this->getCustomErrorMessages("group",$field['normalizeName']);
		$validatorObj =  $this->getValidator("group",$field, $message);
		return  $validatorObj->validate();
	}
	
	private function validateField($field, $value) 
	{
		# add user submitted value;
		$field['user_value'] = $value;
		$validators = $field['validators'];
		unset($field['validators']);
		foreach($validators as $validator) {
			
			# we have nothing to validate if user doesn't fill field and validator is not required or group.
			if(!in_array($validator,array("group","required")) and !$value )
				continue;
			
			$message = $this->getCustomErrorMessages($validator,$field['normalizeName']);
			$validatorObj =  $this->getValidator($validator, $field, $message);
			$result =  $validatorObj->validate();
			
			# if error occur simply return don't try another validators on same field.
			if($result !== 1)
				return $result;
		}
		
		# set callback for custom validation function.
		$result = $this->executeCallbackIfAny($field['normalizeName'],$field);	
		
		return $result;
	}
	
	private function getCustomErrorMessages($validator, $field)
	{
		$message = null;
		# add the custom message to validator level if any specified.
		if(isset($this->customErrorMessages['validator'][$validator]))
			$message = $this->customErrorMessages['validator'][$validator];
		
		# add the custom message to field level if any specified.
		if(isset($this->customErrorMessages['field'][$validator][$field]))
			$message = $this->customErrorMessages['field'][$validator][$field];
		return $message;
	}
	
	public function executeCallbackIfAny($field, $attributes)
	{
		if(isset($this->callbacks[$field])) {
			foreach($this->callbacks[$field] as $callback) {

				$result =  call_user_func_array($callback,array($attributes));
				if($result !== 1)
					return $result;
			}
		}
		return 1;
	}
	
	public function getValidator($validatorName, $field, $message)
	{
		$validatorName = ucfirst($validatorName) ."_validator";
		
		if($this->logger) $this->logger->write("creating '$validatorName' ");

		$load = true;
		
		if(!class_exists($validatorName)) {
			
			if(!file_exists($this->validatorsLocation ."/{$validatorName}.php")) {
				$load = false;
				throw new Exception("File not found for '$validatorName' ");
			}else {
				require_once $this->validatorsLocation ."/{$validatorName}.php";
			}
			
			if(!class_exists($validatorName)) {
				$load = false;
				throw new Exception("Class not found for '$validatorName' ");
			}
		}
			
		if($this->logger) $this->logger->write("class '$validatorName'  found in validators directory ");			
		if($load)
			return new $validatorName($field, $message, $this->config);
	}
	
}