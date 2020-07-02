<?php
/**
* The Recaptcha2 Captcha Driver Class
* @package Venus
*/

namespace Venus\Captcha;

use Venus\App;
use Venus\Helpers\Curl;

/**
* The Recaptcha2 Captcha Driver Class
* Captcha driver which uses Recaptcha2
*/
class Recaptcha2 implements DriverInterface
{
	use \Venus\AppTrait;

	/**
	* Builds the recaptcha2 object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->captcha_recaptcha_public_key || !$this->app->config->captcha_recaptcha_private_key) {
			throw new \Exception('The recaptcha2 public and private keys must be set');
		}

		$this->app->javascript->load('https://www.google.com/recaptcha/api.js');
	}

	/**
	* @see \Venus\Captcha\DriverInterface::check()
	* {@inheritdoc}
	*/
	public function check() : bool
	{
		$curl = new Curl;

		$post_data = [
			'secret' => $this->app->config->captcha_recaptcha_private_key,
			'response' => $this->app->request->post('g-recaptcha-response'),
			'remoteip' => $this->app->user->ip
		];

		$data = $curl->post('https://www.google.com/recaptcha/api/siteverify', $post_data);

		$data = \json_decode($data);

		return $data->success;
	}

	/**
	* @see \Venus\Captcha\DriverInterface::output()
	* {@inheritdoc}
	*/
	public function output()
	{
		echo '<div class="g-recaptcha" data-sitekey="' . $this->app->config->captcha_recaptcha_public_key . '"></div>';
	}
}
