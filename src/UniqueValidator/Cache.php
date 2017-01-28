<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

namespace UniqueValidator;

use Exception;
/**
 * Cache : simple cache class for storing cache and retrieving it.
 */
class Cache 
{
	private $cacheDir;
	
	public function setCacheDir($directory)
	{
		$this->cacheDir = $directory;
	}
	
	public function exists($formUrl)
	{
		return $this->validate($formUrl);
	}
	
	public function validate($formUrl)
	{
		$key = md5($formUrl);
		$formFile = explode("#",$formUrl);
		$formFile = $formFile[0];
		
		if(file_exists($this->cacheDir . "/" . $key)) {
			if(filemtime($formFile) >= filemtime($this->cacheDir . "/" . $key)) {
				return false;
			}
			return $key;
		} else {
			return false;
		}

	}

	public function get($formUrl)
	{
		if($key = $this->validate($formUrl)) {
			return unserialize(file_get_contents($this->cacheDir . "/" .$key));
		} else {
			return false;
		}
	}
	
	public function set($formUrl,$content)
	{
		$key = md5($formUrl);
		if(file_put_contents($this->cacheDir . "/" . $key,serialize($content)) !== false) {
			return $key;
		} else {
			throw new Exception("There is problem while setting cache please set the appropriate permissions to cache directory(".$this->cacheDir.").");
		}
	}	
}