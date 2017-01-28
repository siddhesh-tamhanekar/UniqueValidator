<?php
namespace UniqueValidator;

use parser\Parser;

/**
 *	FormParser : parses the form extract validation rules with corresponding data.
 */

class FormParser 
{
	private $form = "";
	
	private $parser = null;

	private $config = null;

	private $validatationFields = array();
	
	private $fomrMethod = null;

	
	
	public function __construct($parser, $formId,$config = null)
	{
		$this->parser = $parser;
		$this->form = $formId;
	}
	
	public function getParsedArray()
	{
		return $this->validatationFields;
	}
	
	public function getFormMethod()
	{
		return $this->formMethod;
	}
	
	public function parse()
	{
		$form = $this->parser->find("#$this->form");

		# set form method.
		$this->formMethod = $form->getAttribute("method");
		
		# select `select` tags
		$inputs = $form->find("select");
		if($inputs) {
			foreach($inputs as $input) {
				$inputArray = $this->prepareCommonArray($input);
				if($inputArray) {
					$inputArray['type'] = "select";
					
					if(!isset($inputArray['checkvalue'])) {					
						# set of values which is valid for that select ie. option tags.
						$options = $input->find("option");	
						//echo count($options);	
						if($options) {
							foreach($options as $option) {
								if($option->hasAttribute("value")) {
									$inputArray['values'][] = $option->getAttribute("value");
								} else {
									$inputArray['values'][] = $option->textContent;
								}	
							}
						}
					}
					
					$this->validatationFields[$inputArray['normalizeName']] = $inputArray;
					$this->validatationFields[$inputArray['normalizeName']]['validators'] = $this->extractValidators($inputArray);
				}
			}
		}
		
		# select textarea tags
		$inputs = $form->find("textarea");
		if($inputs) {
			foreach($inputs as $input) {
				$inputArray = $this->prepareCommonArray($input);
				if($inputArray){
					$inputArray['type'] = "text";
					$this->validatationFields[$inputArray['normalizeName']] = $inputArray;
					$this->validatationFields[$inputArray['normalizeName']]['validators'] = $this->extractValidators($inputArray);
				}
				
			}
		}
		
		# select input tags
		$inputs = $form->find("input");
		if($inputs) {
			foreach($inputs as $input) {
				
				if($input->getAttribute("type") == "submit")
						continue;
				
				$inputArray = $this->prepareCommonArray($input);
				if($inputArray) {
					
					if($inputArray['type'] == "checkbox" or $inputArray['type'] == "radio") {
						$this->processCheckboxOrRadioInput($inputArray);
						
					} else {
						$this->validatationFields[$inputArray['normalizeName']] = $inputArray;
						
					}
					
					$this->validatationFields[$inputArray['normalizeName']]['validators'] = $this->extractValidators($this->validatationFields[$inputArray['normalizeName']]);
				};
			}
		}

		# select group tags
		$inputs = $form->find("div[@data-uv-group]");
		if($inputs) {
			foreach($inputs as $input) {			
				$inputArray = $this->prepareCommonArray($input);
				if($inputArray) {
					
					$inputArray['fieldType'] = "group";
					$this->validatationFields[$inputArray['normalizeName']] = $inputArray;
					
					$this->validatationFields[$inputArray['normalizeName']]['validators'] = $this->extractValidators($inputArray);
					
					#validators will not contain group validator it should apply to whole group.
					$key = array_search("group", $this->validatationFields[$inputArray['normalizeName']]['validators']);
					if($key !== false) {
						unset($this->validatationFields[$inputArray['normalizeName']]['validators'][$key]);
						
					}
				}				
			}
		}
		//print_R($this->validatationFields);
		return $this->validatationFields;
	}
	
	private function processCheckboxOrRadioInput($inputArray)
	{
		if(isset($this->validatationFields[$inputArray['normalizeName']])) {
			$this->validatationFields[$inputArray['normalizeName']]['values'][] = $inputArray['value'];
		
		} else {
			$inputArray['values'] = array($inputArray['value'] );
			$this->validatationFields[$inputArray['normalizeName']] = $inputArray;
		}

	}

	# returns the validators array by examining attributes.
	private function extractValidators(&$inputArray)
	{
		$validators = array();
		
		$validators = array_merge($validators,$this->attributeValidators($inputArray));
		
		if(strpos($inputArray['name'], "[]")) {
			if(!isset($inputArray['fieldType']) )
				$validators[] = "group";
				
			if(in_array("required",$validators)) {
				
				if(!isset($inputArray['min']))
					$inputArray['min'] = 1;
				
				# delete required from it.
				$index = array_search($required,$validators);
				if($index !== false)
					unset($validators[$index]);
			}
		}
		
		if(isset($inputArray['type']))
			$validators = array_merge($validators,$this->typeValidators($inputArray['type']));
		
		return $validators;
	}
	
	private function attributeValidators($attributes)
	{
		$html5_attrs = array("required","readonly","pattern");
	
		$validators = array();
		foreach($attributes as $attrKey => $attrVal) {
			if(in_array($attrKey,$html5_attrs))
				$validators[] = $attrKey; 

			# if attributes contain with data-uv- or uv- then it's assume as validator.
			if(substr($attrKey,0,3) == "uv-")
				$validators[] = str_ireplace("uv-", "", $attrKey);
		}
		# TODO:: write code for exclude validator.
		return $validators;
	}
	
	private function typeValidators($type)
	{
		$types = array("email", "date", "number", "checkbox", "radio","select","text");
		
		if(in_array($type,$types))
			return array($type);
		else	
			return array();
	}
	
	private function prepareCommonArray($input)
	{
		$inputArray = array();
		if($input->hasAttributes()) {

			foreach($input->attributes as $attribute) {
				$inputArray[str_ireplace("data-","",$attribute->nodeName)] = $attribute->nodeValue;
			}
			
			
			# set name for the field
			if(isset($inputArray['name']))
				$inputArray['name'] = $inputArray['name'];
			else
				return array();
			
			# set the normalizeName for accessing values from submitted form.
			$inputArray['normalizeName'] = str_replace("[]","",$inputArray['name']);
			
			# set label for the field
			if(isset($attributes["label"])) {
				$inputArray['label'] = $attributes['label'];
			} else {
				# get the name from label tag associated with field.
				$labelNode = $this->parser->find("label[@for='$inputArray[name]']");
				if($labelNode and $labelNode->getNodeType() == "single") {
					$inputArray['label'] = $labelNode->nodeValue;
				} else {
					if($labelNode)
						throw new \Exception("There is more than one label tag for the '$inputArray[name]'");
				}
			}

			# if label is not set set the name attribute as name
			if(!isset($inputArray['label']))
				$inputArray['label'] = $inputArray['normalizeName'];
			
			if(strstr($inputArray['name'],"[]")) {
				$inputArray['expectedValue'] = "array";
			} else {
				$inputArray['expectedValue'] = "string";
			}
		}

		return $inputArray;
	}
}