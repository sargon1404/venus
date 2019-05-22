<?php
/**
* The Captcha Class
* @package Venus
*/

namespace Venus;

use Venus\Captcha\DriverInterface;

/**
* The Captcha Class
* Class which provides captcha functionality
*/
class Captcha
{
	use AppTrait;

	/**
	* @var bool $enabled Will be set to true, if captcha is enabled
	*/
	protected $enabled = false;

	/**
	* @var string $driver The used driver
	*/
	protected $driver = '';

	/**
	* @var object $handle The driver's handle
	*/
	protected $handle = null;

	/**
	* Builds the captcha object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		if (!$this->app->config->captcha_enable) {
			return;
		}

		$this->driver = $this->app->config->captcha_engine;
		$this->handle = $this->getHandle();
		$this->enabled = true;
	}

	/**
	* Returns the handle corresponding to the driver
	* @return DriverInterface The driver handle
	*/
	protected function getHandle(): DriverInterface
	{
		$handle = null;

		switch ($this->driver) {
			case 'recaptcha2':
			default:
				$handle = new Recaptcha2($this->app);
		}

		$this->app->plugins->run('captchaGetHandle', $this->driver, $handle, $this);

		if (!$handle instanceof DriverInterface) {
			throw new \Exception('The captcha driver must implement interface DriverInterface');
		}

		return $handle;
	}

	/**
	* Checks the captcha is correct
	* @return bool Returns bool if the captcha is correct
	*/
	public function check() : bool
	{
		if (!$this->enabled) {
			return true;
		}

		return $this->handle->check();
	}

	/**
	* Outputs the captcha
	*/
	public function output()
	{
		if (!$this->enabled) {
			return true;
		}

		echo '<div class="captcha">';
		$this->handle->output();
		echo '</div>';
	}
}
