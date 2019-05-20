<?php
/**
* The Time Class
* @package Venus
*/

namespace Venus;

/**
* The Time Class
* Time related functions
*/
class Time extends \Mars\Time
{
	use AppTrait;

	/**
	* Builds the time object
	* Sets the default timezone to UTC
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		date_default_timezone_set('UTC');
	}

	/**
	* @see \Mars\Time::adjust()
	* {@inheritDoc}
	*/
	public function adjust(int $timestamp, ?int $timezone_offset = null) : int
	{
		if ($timezone_offset === null) {
			$timezone_offset = $this->app->user->timezone_offset;
		}

		return parent::adjust($timestamp, $timezone_offset);
	}

	/**
	* @see \Mars\Time::adjustUtc()
	* {@inheritDoc}
	*/
	public function adjustUtc(int $timestamp, ?int $timezone_offset = null) : int
	{
		if ($timezone_offset === null) {
			$timezone_offset = $this->app->user->timezone_offset;
		}

		return parent::adjustUtc($timestamp, $timezone_offset);
	}

	/**
	* Converts a date time pattern to a timestamp/msqyl datetime
	* @param string $date The date value Eg: 05/10/2012
	* @param string $time The time value eg: 06:23:34
	* @param bool $return_timestamp If true will return a timestamp. If false will return a mysql datetime
	* @param bool $adjust_timestamp If true and $return_timestamp is true will return the timestamp adjusted to UTC
	* @param string $date_pattern The pattern of the date value Eg: mm/dd/yyyy. If empty, $this->app->lang->date_picker_format is used
	* @param string $time_pattern The pattern of the time value Eg: hh:mm:ss. If empty, $this->app->lang->time_picker_format is used
	* @return mixed Returns a timestamp or a mysql dateime based on $return_timestamp
	*/
	public function toDatetime(string $date, string $time, bool $return_timestamp = true, bool $adjust_timestamp = true, string $date_pattern = '', string $time_pattern = '')
	{
		if (!$date) {
			return 0;
		}
		if (!$time || $time == '.') {
			$time = str_replace(['hh', 'mm', 'ss'], ['00', '00', '00'], $this->app->lang->time_picker_format);
		}

		if (!$date_pattern) {
			$date_pattern = $this->app->lang->date_picker_format;
		}
		if (!$time_pattern) {
			$time_pattern = $this->app->lang->time_picker_format;
		}

		$date_array = [];
		$time_array = [];

		$pattern = str_replace(['dd', 'mm', 'yyyy'], '(.*)', preg_quote($date_pattern, '/'));
		if (!preg_match("/{$pattern}/", $date_pattern, $m)) {
			return false;
		}
		if (!preg_match("/{$pattern}/", $date, $date_m)) {
			return false;
		}

		$m_length = count($m);
		for ($i = 1; $i < $m_length; $i++) {
			$part = $m[$i];
			$date_array[$part] = (int)$date_m[$i];
		}

		$pattern = str_replace(['hh', 'mm', 'ss'], '(.*)', preg_quote($time_pattern, '/'));
		if (!preg_match("/{$pattern}/", $time_pattern, $m)) {
			return 0;
		}
		if (!preg_match("/{$pattern}/", $time, $time_m)) {
			return 0;
		}

		$m_length = count($m);
		for ($i = 1; $i < $m_length; $i++) {
			$part = $m[$i];
			$time_array[$part] = (int)$time_m[$i];
		}

		if (!$this->app->validator->isDatetime($date_array['yyyy'], $date_array['mm'], $date_array['dd'], $time_array['hh'], $time_array['mm'], $time_array['ss'])) {
			return false;
		}

		if ($return_timestamp) {
			$dt = new \DateTime;
			$dt->setDate($date_array['yyyy'], $date_array['mm'], $date_array['dd']);
			$dt->setTime($time_array['hh'], $time_array['mm'], $time_array['ss']);

			if ($adjust_timestamp) {
				return $this->adjustUtc($dt->getTimestamp());
			} else {
				return $dt->getTimestamp();
			}
		} else {
			return $date_array['yyyy'] . '-' . $date_array['mm'] . '-' . $date_array['dd'] . ' ' . $time_array['hh'] . ':' . $time_array['mm'] . ':' . $time_array['ss'];
		}
	}

	/**
	* Converts a date pattern to a timestamp/mysql date
	* @param string $date The date value Eg: 05/10/2012
	* @param bool $return_timestamp If true will return a timestamp. If false will return a mysql datetime
	* @param bool $adjust_timestamp If true and $return_timestamp is true will return the timestamp adjusted to UTC
	* @param string $date_pattern The pattern of the date value Eg: mm/dd/yyyy. If empty, $this->app->lang->date_picker_format is used
	* @return mixed Returns a timestamp or a mysql dateime based on $return_timestamp
	*/
	public function toDate(string $date, bool $return_timestamp = true, bool $adjust_timestamp = true, string $date_pattern = '')
	{
		if (!$date) {
			return 0;
		}

		if (!$date_pattern) {
			$date_pattern = $this->app->lang->date_picker_format;
		}

		$date_array = [];

		$pattern = str_replace(['dd', 'mm', 'yyyy'], '(.*)', preg_quote($date_pattern, '/'));

		if (!preg_match("/{$pattern}/", $date_pattern, $m)) {
			return false;
		}
		if (!preg_match("/{$pattern}/", $date, $date_m)) {
			return false;
		}

		$m_length = count($m);
		for ($i = 1; $i < $m_length; $i++) {
			$part = $m[$i];
			$date_array[$part] = (int)$date_m[$i];
		}

		if (!$this->app->validator->isDate($date_array['yyyy'], $date_array['mm'], $date_array['dd'])) {
			return false;
		}

		if ($return_timestamp) {
			$dt = new \DateTime;
			$dt->setDate($date_array['yyyy'], $date_array['mm'], $date_array['dd']);
			if ($adjust_timestamp) {
				return $this->adjustUtc($dt->getTimestamp());
			} else {
				return $dt->getTimestamp();
			}
		} else {
			return $date_array['YYYY'] . '-' . $date_array['MM'] . '-' . $date_array['DD'];
		}
	}

	/**
	* Returns all the timezones
	* @return array The timezones, divided by continent/region
	*/
	public function getTimezones() : array
	{
		$now = time();
		$regions = [];
		$timezones = timezone_identifiers_list();

		foreach ($timezones as $tz) {
			if ($tz == 'UTC') {
				continue;
			}

			$parts = explode('/', $tz);
			$region = $parts[0];
			$name = $parts[1];

			if (count($parts) > 2) {
				$name = implode('-', array_slice($parts, 1));
			}

			$name = str_replace('_', ' ', $name);

			if (!isset($regions[$region])) {
				$regions[$region] = [];
			}

			$regions[$region][] = [$tz, $name];
		}

		return $regions;
	}
}
