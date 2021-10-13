<?php
/**
* The Bin Output Class
* @package Venus
*/

namespace Venus\Bin\System;

/**
* The Bin Output Class
*/
class Output extends \Venus\System\Output
{
		
	/**
	* @see \Venus\System\Bin::message()
	* {@inheritdoc}
	*/
	public function message(string $text)
	{
		parent::message($text);
		
		$this->app->bin->message($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Bin::error()
	* {@inheritdoc}
	*/
	public function error(string $text)
	{
		parent::error($text);
		
		$this->app->bin->error($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Bin::warning()
	* {@inheritdoc}
	*/
	public function warning(string $text)
	{
		parent::warning($text);
		
		$this->app->bin->warning($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Bin::info()
	* {@inheritdoc}
	*/
	public function info(string $text)
	{
		parent::info($text);
		
		$this->app->bin->info($text);
		
		return $this;
	}
}
