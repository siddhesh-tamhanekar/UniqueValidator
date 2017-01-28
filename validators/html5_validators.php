<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */


use UniqueValidator\AbstractValidator;

# this file contains the most validators.
class Required_validator extends AbstractValidator
{
	public function validate()
	{

		if(trim($this->data['user_value']) !="")
			return 1;
		else
			return $this->setMessage("%label% field is required");
	}
}

class Email_validator extends AbstractValidator
{
	public function validate()
	{
		if(preg_match("|\b[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}\b|",$this->data['user_value']))
			return 1;
		else
			return $this->setMessage("%label% is doesn't seem to be valid email address.");
	}
}

class Text_validator extends AbstractValidator
{
	public function validate()
	{
		if(isset($this->data['min']) and!is_numeric($this->data['min']))
			return 1;
		if(isset($this->data['min']) and isset($this->data['max'])) {
			if (strlen($this->data['user_value']) >= $this->data['min'] and strlen($this->data['user_value']) <= $this->data['max'] )
				return 1;
			else
				return $this->setMessage("%label% is should  be between %min% to %max%.");
			
		}
		
		if(isset($this->data['min']) ) {
			if (strlen($this->data['user_value']) >= $this->data['min']  )
				return 1;
			else
				return $this->setMessage("%label% field atleast contain  %min% characters");
		}
		
		if( isset($this->data['max'])) {
			if (strlen($this->data['user_value']) <= $this->data['max'] )
				return 1;
			else
				return $this->setMessage("%label%  can contain only %max% character atmost.");
		}
		return 1;
	}
}

class Readonly_validator extends AbstractValidator
{
	public function validate()
	{
		
		if($this->data['user_value'] == $this->data['value'])
			return 1;
		else
			return $this->setMessage("%label% field cannot be changed");
	}
}

class Pattern_validator extends AbstractValidator
{
	public function validate()
	{
		
		if(preg_match("/".$this->data['pattern']."/",$this->data['user_value']))
			return 1;
		else
			return $this->setMessage("%label% doesn't seem to be in valid format.");
	}
}


class Number_validator extends AbstractValidator
{
	public function validate()
	{
		if(is_numeric($this->data['user_value'])) {
			
			if(isset($this->data['min']) and isset($this->data['max'])) {
				if ($this->data['user_value'] >= $this->data['min'] and $this->data['user_value'] <= $this->data['max'] )
					return 1;
				else
					return $this->setMessage("%label% should be between %min% to %max% number.");
				
			}
			
			if(isset($this->data['min']) ) {
				if ($this->data['user_value'] >= $this->data['min']  )
					return 1;
				else
					return $this->setMessage("%label% is should  be greater than %min%");
			}
			
			if( isset($this->data['max'])) {
				if ($this->data['user_value'] <= $this->data['max'] )
					return 1;
				else
					return $this->setMessage("%label% is should  be lesser than %max%");
			}
				
			return 1;
		}
		else
			return $this->setMessage("%label% is  not a valid number.");
	}
}


class Select_validator extends AbstractValidator
{
	public function validate()
	{
		if(isset($this->data['checkvalue']))
			return 1;

		if(isset($this->data['multiple'])) {
			if(!array_diff($this->data['user_value'],$this->data['values']))
			{
				return 1;
			}else {
				
				return $this->setMessage("%label% is not contained valid values");
			}
		} else {
			if(in_array($this->data['user_value'],$this->data['values']))
				return 1;
			else
				return $this->setMessage("%label% is not contained valid value");
			
		}
	
	}
}

class Checkbox_validator extends AbstractValidator
{
	public function validate()
	{
		if($this->data['expectedValue']== "array") {
			if(!array_diff($this->data['user_value'],$this->data['values']))
			{
				return 1;
			}else {
				
				return $this->setMessage("%label% is not contained valid values");
			}
		} else {
			if(in_array($this->data['user_value'],$this->data['values']))
				return 1;
			else
				return $this->setMessage("%label% is not contained valid value");
			
		}
	}
}

class Radio_validator extends AbstractValidator
{
	public function validate()
	{
		if(in_array($this->data['user_value'],$this->data['values']))
			return 1;
		else
			return $this->setMessage("%label% is not contained valid value");	
	}
}


class Date_validator extends AbstractValidator
{
	public function validate()
	{
		
		$format = "Y-m-d";
		if(isset($this->data['format']))
			$format = $this->data['format'];
		
		$d = DateTime::createFromFormat($format, $this->data['user_value']);
		
		/* echo "<pre>";
		echo "Use submtted:" .$this->data['user_value'];
		echo "Use MIn:" .$this->data['min'];
		echo "Use Max:" .$this->data['max'];
		echo "compiled submtted date from dateObject:". $d->format($format);
		print_r($d); */
		$result  = $d && $d->format($format) == $this->data['user_value'];
		if($result)
		{
			$date = strtotime($d->format("Y-m-d"));
			if(isset($this->data['min'])) {
				if($this->data['min'] == "today")
					$min = strtotime(date("Y-m-d"));
				else
					$min = strtotime(DateTime::createFromFormat($format, $this->data['min'])->format("Y-m-d"));
				
			}
			if(isset($this->data['max'])) {
				if($this->data['max'] == "today")
					$max = strtotime(date("Y-m-d"));
				else
					$max = strtotime(DateTime::createFromFormat($format, $this->data['max'])->format("Y-m-d"));
				
			}
			//echo "submitted : ". $date . " |  min : ". $min ." | max : ".$max;
			if(isset($this->data['min']) and isset($this->data['max'])) {
				
				if ( $date >= $min and $date <= $max)
					return 1;
				else
					return $this->setMessage("%label% is should  be between %min% to %max%");
				
			}
			
			if(isset($this->data['min']) ) {
				if ($date >= $min  )
					return 1;
				else
					return $this->setMessage("%label% is should not be lesser than %min%.");
			}
			if( isset($this->data['max'])) {
				if ($date <= $max )
					return 1;
				else
					return $this->setMessage("%label% is should not be greater than %max%");
			}
			return 1;
		} else {
			return $this->setMessage("%label% is not valid date or in invalid format");
		}
		

	}
}

class Url_validator extends AbstractValidator
{
	# regular expression is used from https://gist.github.com/dperini/729294
	# demo link for https://mathiasbynens.be/demo/url-regex;
	public function validate()
	{
		if(preg_match("%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iuS",$this->data['user_value']))
			return 1;
		else
			return $this->setMessage("%label% is doesn't seem to be valid url address.");

	}
}

# TODO:: the following validator has not completed please don't use it.
class Unique_validator extends AbstractValidator
{
	public function validate()
	{
		if($this->config->get("db") or get_class($this->config->get("db")) != "Mysqli") {
			throw new Exception("Please set Mysqli DB Object in config through setConfig() method of Uvalidator");
		}
		$unique = $this->data['data-uv-unique'];
		$data = explode(".",$data);
		$stmt = $this->config->get("db")->prepare("select * from $data[0] where $data[1]=?");
		$stmt->bind_param("s",$this->data['user_value']);
		$res = $stmt->execute();
		$res = $stmt->get_result();
		//var_dump($res);
		if($res->num_rows)
			return $this->setMessage("%label% is not available");
		else
			return 1;
	}
	
}

# refactor it.
class Checkwith_validator extends AbstractValidator
{
	public function validate()
	{
		if($this->config->get("db") or get_class($this->config->get("db")) != "Mysqli") {
			throw new Exception("Please set Mysqli DB Object in config through setConfig() method of Uvalidator");
		}
		$data = $this->data['uv-checkwith'];
		$data = explode(".",$data);
		$stmt = $this->db->prepare("select * from $data[0] where $data[1]=?");
		$stmt->bind_param("s",$this->data['user_value']);
		$res = $stmt->execute();
		$res = $stmt->get_result();
		if($res->num_rows)
			return 1;
		else
			return $this->setMessage("%label% has not valid value");
	}
}

//TODO:: add file vaildator
class File_validator extends AbstractValidator 
{
	public function validate()
	{
		# check if required
		# check if size 
		# check it's type
		return 1;
	}
}