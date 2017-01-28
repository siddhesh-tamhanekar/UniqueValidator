<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

use UniqueValidator\AbstractValidator;

class Default_validator extends AbstractValidator
{
	public function validate()
	{
		if($this->data['user_value'] != $this->data['value'])
			return 1;
		else
			return $this->setMessage("%label% contains default value.");
	}
}