<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

use UniqueValidator\AbstractValidator;

class Group_validator extends AbstractValidator
{
	public function validate()
	{
		if(is_string($this->data['user_value']) and !$this->data['user_value'] )
			$this->data['user_value'] = array();

		$this->data['user_value'] = array_map("trim", $this->data['user_value'] );
		
		# remove empty values. 
		$this->data['user_value'] =array_diff( $this->data['user_value'], array( '' ) ); 
		
		if(isset($this->data['min'] ) and isset($this->data['max'] )) {
			if(count($this->data['user_value']) >= $this->data['min'] 
			and count($this->data['user_value'])<= $this->data['max']) {
				return 1;
			} else
				return $this->setMessage("%label% should be between %min% to %max%.");
			
		} elseif(isset($this->data['min'] )) {
			if(count($this->data['user_value']) >= $this->data['min'] ) 
				return 1;
			else{
				return $this->setMessage("Minimum %min% %label% are required.");
			}
			
		} elseif(isset($this->data['max'] )){
			
			if(count($this->data['user_value']) <= $this->data['max'] ) 
				return 1;
			else
				return $this->setMessage("Maximum %max% %label% are allowed.");
			
		}
		
	}
}