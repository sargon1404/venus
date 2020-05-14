<?php
/**
* The System's Output Class
* @package Venus
*/

namespace Venus\System;

/**
* The System's Output Class
*/
class Output
{
	use \Venus\AppTrait;
	
	/**
	* @var string $file The file to output to
	*/
	protected string $file = 'system';
	
	/**
	* Outputs a message
	* @param string $text The text to output
	* @return $this
	*/
	public function message(string $text)
	{
		$this->app->log->log($this->file, $text);
		
		return $this;
	}
	
	/**
	* Outputs an error
	* @param string $text The text to output
	* @return $this
	*/
	public function error(string $text)
	{
		$this->app->log->log($this->file, '***[ERROR]*** ' . $text);
		
		return $this;
	}
	
	/**
	* Outputs a warning
	* @param string $text The text to output
	* @return $this
	*/
	public function warning(string $text)
	{
		$this->app->log->log($this->file, '[WARNING] ' . $text);
		
		return $this;
	}
	
	/**
	* Outputs an info
	* @param string $text The text to output
	* @return $this
	*/
	public function info(string $text)
	{
		$this->app->log->log($this->file, '[INFO] ' . $text);
		
		return $this;
	}
}
