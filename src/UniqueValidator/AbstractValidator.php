<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace UniqueValidator;

# every validator should be inherited from this class.
abstract class AbstractValidator
{
	protected $message;
	
	protected $data;
	
	protected $config;

	public function __construct($data, $message, $config = null)
	{
		$this->message = $message;
		$this->data = $data;
		$this->config = $config;		
	}

	# set the error message.
	protected function setMessage($default_message)
	{
		
		if(isset($this->message))		
			$default_message = $this->message;

		preg_match_all("/%.*?%/",$default_message,$matches);
		$this->data['label'] = $this->data['label']; 
		foreach($matches[0] as $match)
		{
			$key = str_replace("%","",$match);
			$default_message = str_replace($match,$this->data[$key],$default_message);
		}
		return $default_message;
	}
	
	abstract function validate();
}