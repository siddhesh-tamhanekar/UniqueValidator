<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace UniqueValidator;

/**
 * Config : simple container for configurations. it can contain the resouces for use to  store settings, resources,
 * some defined values for formparser and validators.
 */
class Config 
{
	private $config =array();
	
	public function get($key)
	{
		if(isset($this->config[$key])) {
			return $this->config[$key];
		}else
			return false;
	}
	
	public function set($key,$value)
	{
		$this->config[$key] = $value;
	}
	
}