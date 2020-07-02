<?php
/**
* The HTML Class
* @package Venus
*/

namespace Venus;

/**
* The HTML Class
* Html generating methods
*/
class Html extends \Mars\Html
{
	/**
	* Builds the object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		parent::__construct($app);

		$supported_tags = [
			'timezone' => '\Venus\Html\Input\Timezone'
		];

		$this->supported_tags = array_merge($this->supported_tags, $supported_tags);
	}

	/**
	* Outputs the token in a hidden field
	* @return string The token, if $return is true
	*/
	public function token()
	{
		echo $this->getToken();
	}

	/**
	* Returns the token
	* @return string The token
	*/
	public function getToken() : string
	{
		return $this->inputHidden($this->app->config->token_field, $this->app->user->token);
	}

	/**
	* Outputs the response field
	* @param string $response The value of the response field
	* @return string The response fuekd
	*/
	public function response($response = '')
	{
		echo $this->getResponse($response);
	}

	/**
	* Returns the response field
	* @param string $response The value of the response field
	* @return string
	*/
	public function getResponse($response = '') : string
	{
		return $this->inputHidden($this->app->config->response_param, $response);
	}

	/**
	* Outputs the ajax field
	* @return string
	*/
	public function ajax()
	{
		echo $this->getAjax();
	}

	/**
	* Returns the ajax field
	* @return string
	*/
	public function getAjax() : string
	{
		return $this->getResponse('ajax');
	}

	/**
	* Returns two radio controls. One with a "Yes" value, the other with "No"
	* @param string $name The name of the field
	* @param bool $yes If true the Yes control will be checked. Otherwise 'no' will be checked
	* @param array $attributes Extra attributes in the format name => value, which will be applied to all radios
	* @return string The html code
	*/
	public function radioYesNo(string $name, bool $yes = true, array $attributes = []) : string
	{
		$values = [1 => App::__('yes'), 0 => App::__('no')];

		return $this->radioGroup($name, $values, $yes ? 1 : 0, $attributes);
	}

	/**
	* Returns two form radio controls. One with a "On" value,the other with "Off"
	* @param string $name The name of the field
	* @param bool $on If true the On control will be checked. Otherwise Off will be checked
	* @param array $attributes Extra attributes in the format name => value, which will be applied to all radios
	* @return string The html code
	*/
	public function radioOnOff(string $name, bool $on = true, array $attributes = []) : string
	{
		$values = [1 => App::__('on_off1'), 0 => App::__('on_off2')];

		return $this->radioGroup($name, $values, $on ? 1 : 0, $attributes);
	}

	/**
	* Returns a control from where the user will be able to select the timezone
	* @param string $name The name of the select control
	* @param string $selected The name of the option that should be selected
	* @param bool $required If true,it will be a required control
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function timezone(string $name, string $selected = '', bool $required = false, array $attributes = []) : string
	{
		$attributes = $attributes + ['name' => $name, 'required' => $required];

		return $this->getTag('timezone', $attributes, ['selected' => $selected])->get();
	}
}
