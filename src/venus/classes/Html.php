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
	* @return string The html code
	*/
	public function radioYesNo(string $name, bool $yes = true) : string
	{
		return $this->radio($name, App::__('yes'), $yes, 1) . $this->radio($name, App::__('no'), !$yes, 0);
	}

	/**
	* Returns two form radio controls. One with a "On" value,the other with "Off"
	* @param string $name The name of the field
	* @param bool $on If true the On control will be checked. Otherwise Off will be checked
	* @return string The html code
	*/
	public function radioOnOff(string $name, bool $on = true) : string
	{
		return $this->radio($name, App::__('on_off1'), $on, 1) . $this->radio($name, App::__('on_off2'), !$on, 0);
	}

	/**
	* Returns controls from where the user will be able to select the date and time
	* @param string $name The name of the control. The name of the date control will be $name_date, the time= $name_time
	* @param int $timestamp The timestamp of the datetime control.
	* @param string $date If specified, the value of the date part will be set to $date
	* @param string $time If specified, the value of the time part will be set to $date
	* @param bool $required If true the datetime control will be a required control
	* @return string The html code
	*/
	public function selectDatetime(string $name, int $timestamp = 0, string $date = '0', string $time = '0', bool $required = false) : string
	{
		[$date, $time] = $this->getDateTime($timestamp, $date, $time);

		$html = $this->input($name . '-date', $date, $required, $this->app->lang->date_picker_format, 'date') . '&nbsp;';
		$html.= $this->input($name . '-time', $time, $required, $this->app->lang->time_picker_format, 'date');

		return $this->app->plugins->filter('html_select_datetime', $html, $name, $timestamp, $date, $time, $required, $this);
	}

	/**
	* Returns a control from where the user will be able to select the date
	* @param string $name The name of the control.
	* @param int $timestamp The timestamp of the date control.
	* @param string $date If specified, the value of the date part will be set to $date
	* @param bool $required If true the date control will be a required control
	* @return string The html code
	*/
	public function selectDate(string $name, int $timestamp = 0, string $date = '0', bool $required = false) : string
	{
		[$date, $time] = $this->getDateTime($timestamp, $date, 0);

		$html = $this->input($name, $date, $required, $this->app->lang->date_picker_format, 'date');

		return $this->app->plugins->filter('html_select_date', $html, $name, $timestamp, $date, $required, $this);
	}

	/**
	* Returns a control from where the user will be able to select the time of the day
	* @param string $name The name of the control.
	* @param int $timestamp The timestamp of the date control.
	* @param string $time If specified, the value of the time part will be set to $date
	* @param bool $required If true the date control will be a required control
	* @return string The html code
	*/
	public function selectTime(string $name, int $timestamp = 0, string $time = '0', bool $required = false) : string
	{
		[$date, $time] = $this->getDateTime($timestamp, 0, $time);

		$html = $this->input($name, $time, $required, '', 'time');

		return $this->app->plugins->filter('html_select_time', $html, $name, $timestamp, $time, $required, $this);
	}

	/**
	* Computes the date and time to be shown in a select_datetime control
	* @param int $timestamp The timestamp
	* @param string $date The date part
	* @param string $time The time part
	* @return array
	*/
	protected function getDateTime(int $timestamp, string $date, string $time) : array
	{
		if ($timestamp) {
			$d = getdate($this->app->time->adjust($timestamp));

			if ($date === '0') {
				$date = str_replace(['dd', 'mm', 'yyyy'], [App::padInt($d['mday']), App::padInt($d['mon']), $d['year']], $this->app->lang->date_picker_format);
			}
			if ($time === '0') {
				$time = str_replace(['hh', 'mm', 'ss'], [App::padInt($d['hours']), App::padInt($d['minutes']), App::padInt($d['seconds'])], $this->app->lang->time_picker_format);
			}
		} else {
			if ($date === '0') {
				$date = '';
			}
			if ($time === '0') {
				$time = '';
			}
		}

		return [$date, $time];
	}

	/**
	* Returns a control from where the user will be able to select the timezone
	* @param string $name The name of the select control
	* @param mixed $selected The name of the option that should be selected
	* @param bool $required If true,it will be a required control
	* @return string The html code
	*/
	public function selectTimezone(string $name, string $selected = '', bool $required = false) : string
	{
		$regions = ['Africa' => 'Africa', 'America' => 'America', 'Asia' => 'Asia', 'Australia' => 'Australia',
						'Europe' => 'Europe', 'Indian' => 'Indian', 'Pacific' => 'Pacific', 'Atlantic' => 'Atlantic',
						'Antarctica' => 'Antarctica', 'Arctic' => 'Arctic'];

		$options = [];
		$options['UTC'] = ['UTC', false];
		$regions_array = $this->app->time->getTimezones();

		foreach ($regions_array as $region => $timezones) {
			$region_name = $regions[$region];
			$options[$region_name] = [$region_name, true];

			foreach ($timezones as $timezone) {
				$options[$timezone[0]] = [$timezone[1], false];
			}
		}

		$html = $this->select($name, $options, $selected, $required);

		return $this->app->plugins->filter('html_select_timezone', $html, $name, $options, $selected, $required, $this);
	}
}
