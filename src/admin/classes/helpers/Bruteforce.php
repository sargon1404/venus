<?php
/**
* The Bruteforce Protection Class
* @package Venus
*/

namespace Venus\Admin\Helpers;

/**
* The Bruteforce Protection Class
* Protects agains bruteforce attempts
*/
class Bruteforce extends \Venus\Helpers\Bruteforce
{
	/**
	* @var string $scope The bruteforce scope
	*/
	protected string $scope = 'admin';

	/**
	* Builds the Bruteforce object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		$this->ip_max_attemps = $this->app->config->bruteforce_ip_max_attemps;
		$this->ip_block_seconds = $this->app->config->bruteforce_ip_block_seconds;
		$this->user_max_attemps = $this->app->config->bruteforce_user_max_attemps;
		$this->user_block_seconds = $this->app->config->bruteforce_user_block_seconds;
	}
}
