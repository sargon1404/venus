<?php
/**
* The CLI Output Class
* @package Venus
*/

namespace Venus\Cli\System;

/**
* The CLI Output Class
*/
class Output extends \Venus\System\Output
{
		
	/**
	* @see \Venus\System\Cli::message()
	* {@inheritdoc}
	*/
	public function message(string $text)
	{
		parent::message($text);
		
		$this->app->cli->message($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Cli::error()
	* {@inheritdoc}
	*/
	public function error(string $text)
	{
		parent::error($text);
		
		$this->app->cli->error($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Cli::warning()
	* {@inheritdoc}
	*/
	public function warning(string $text)
	{
		parent::warning($text);
		
		$this->app->cli->warning($text);
		
		return $this;
	}
	
	/**
	* @see \Venus\System\Cli::info()
	* {@inheritdoc}
	*/
	public function info(string $text)
	{
		parent::info($text);
		
		$this->app->cli->info($text);
		
		return $this;
	}
}
