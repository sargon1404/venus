<?php
/**
* The Format Class
* @package Venus
*/

namespace Venus;

/**
* The Format Class
* Converts values using a certain format
*/
class Format extends \Mars\Format
{
	/**
	* @see \Mars\Format::number()
	* {@inheritDoc}
	*/
	public function number(float $number, int $decimals = 0, string $dec_point = '.', string $thousands_sep = ',') : string
	{
		return parent::number($number, $decimals, $this->app->lang->decimal_separator, $this->app->lang->thousands_separator);
	}

	/**
	* @see \Mars\Format::size()
	* {@inheritDoc}
	*/
	public function size(int $kb, int $digits = 2, string $gb_str = 'GB', string $mb_str = 'MB', string $kb_str = 'KB') : string
	{
		return parent::size($kb, $digits, App::__('gb'), App::__('mb'), App::__('kb'));
	}

	/**
	* Formats an IP
	* @param string $ip The ip address
	* @return string The formatted IP
	*/
	public function ip(string $ip) : string
	{
		return $ip . $this->countryFromIp($ip);
	}

	/**
	* Returns the country as determined from IP
	* @param string $ip The ip address
	* @return string The country name
	*/
	public function countryFromIp(string $ip) : string
	{
		if (!$this->app->config->geoip_enable) {
			return '';
		}

		if (!isset($this->app->geoip)) {
			$this->app->geoip = new Helpers\Geoip;
		}

		$country = $this->app->geoip->getCountry($ip);
		if (!$country) {
			$country = App::__('unknown');
		}

		return ' [' . $country . ']';
	}

	/**
	* Formats a timestamp
	* @param int $timestamp The timestamp
	* @param string $format The format in which the date will be formatted. Identical with the one used with date(). By default the $this->app->lang->dateformat will be used
	* @param bool $adjust If true, will adjust the timestamp to the user's timezone
	* @param bool $replace_strings If true, will replace the english strings with the current language's strings
	* @return string The formatted date
	*/
	public function timestamp(int $timestamp = 0, string $format = '', bool $adjust = true, bool $replace_strings = true) : string
	{
		///is it a timestamp or a msqyl date in the yyyy-mm-dd mm:ss format?
		if (!is_numeric($timestamp)) {
			$timestamp = $this->app->db->datetimeToTimestamp($timestamp);
		}
		if (!$timestamp) {
			return App::__('unknown');
		}

		if (!$format) {
			$format = $this->app->lang->timestamp_format;
		}

		if ($adjust) {
			$timestamp = $this->app->time->adjust($timestamp);
		}

		if ($replace_strings) {
			$time = getdate($timestamp);

			$format = str_replace(['F', 'M', 'D', 'I'], ['{\F}', '{\M}', '{\D}', '{\I}'], $format);
			$search = ['{F}', '{M}', '{D}', '{l}'];
			$replace = [App::__('month' . $time['mon']), App::__('short_month' . $time['mon']), App::__('short_day' . $time['wday']), App::__('day' . $time['wday'])];
		}

		$result = date($format, $timestamp);
		if ($replace_strings) {
			$result = str_replace($search, $replace, $result);
		}

		return $result;
	}

	/**
	* Alias of timestamp
	* @param int $timestamp The timestamp
	* @param string $format The format in which the date will be formatted. Identical with the one used with date(). By default the $this->app->lang->dateformat will be used
	* @param bool $adjust If true, will adjust the timestamp to the user's timezone
	* @param bool $replace_strings If true, will replace the english strings with the current language's strings
	* @return string The formatted date
	*/
	public function datetime(int $timestamp = 0, string $format = '', bool $adjust = true, bool $replace_strings = true) : string
	{
		return $this->timestamp($timestamp, $format, $adjust, $replace_strings);
	}

	/**
	* Formats a date
	* @param int $timestamp The timestamp
	* @param bool $adjust If true, will adjust the timestamp to the user's timezone
	* @param bool $replace_strings If true, will replace the english strings with the current language's strings
	* @return string The formatted date
	*/
	public function date(int $timestamp = 0, bool $adjust = true, bool $replace_strings = true) : string
	{
		return $this->timestamp($timestamp, $this->app->lang->date_format, $adjust, $replace_strings);
	}

	/**
	* Formats a date
	* @param int $timestamp The timestamp
	* @param bool $adjust If true, will adjust the timestamp to the user's timezone
	* @param bool $replace_strings If true, will replace the english strings with the current language's strings
	* @return string The formatted time
	*/
	public function time(int $timestamp = 0, bool $adjust = true, bool $replace_strings = true) : string
	{
		return $this->timestamp($timestamp, $this->app->lang->time_format, $adjust, $replace_strings);
	}

	/**
	* Returns the date part of $timestamp in the date_picker_format format
	* @param int $timestamp The timestamp
	* @return string Returns the timestamp in the date_picker format
	*/
	public function datePicker(int $timestamp) : string
	{
		if (!$timestamp) {
			return '';
		}

		$d = getdate($timestamp);

		return str_replace(['dd', 'mm', 'yyyy'], [App::padInt($d['mday']), App::padInt($d['mon']), $d['year']], $this->app->lang->date_picker_format);
	}

	/**
	* Returns the time part of $timestamp in the time_picker_format format
	* @param int $timestamp The timestamp
	* @return string Returns the timestamp in the time_picker format
	*/
	public function timePicker(int $timestamp) : string
	{
		if (!$timestamp) {
			return '';
		}

		$d = getdate($timestamp);

		return str_replace(['hh', 'mm', 'ss'], [App::padInt($d['hours']), App::padInt($d['minutes']), App::padInt($d['seconds'])], $this->app->lang->time_picker_format);
	}
}