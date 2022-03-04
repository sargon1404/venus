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
	* Returns the current time(), adjusted to the user's timezone
	* @return int
	*/
	public function current() : int
	{
		return $this->adjust(time());
	}

	/**
	* @see \Mars\Time::adjust()
	* {@inheritdoc}
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
	* {@inheritdoc}
	*/
	public function adjustUtc(int $timestamp, ?int $timezone_offset = null) : int
	{
		if ($timezone_offset === null) {
			$timezone_offset = $this->app->user->timezone_offset;
		}

		return parent::adjustUtc($timestamp, $timezone_offset);
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
