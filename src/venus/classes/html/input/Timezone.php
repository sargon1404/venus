<?php
/**
* The Timezone Class
* @package Venus
*/

namespace Venus\Html\Input;

use Mars\App;
use Mars\Html\Input\Select;

/**
* The Timezone Class
* Renders a field from where the timezone can be picked
*/
class Timezone extends \Mars\Html\Tag
{
	/**
	* @var string $selected The selected timezone, if any
	*/
	public string $selected = '';

	/**
	* @var array $regions Array listing the available regions
	*/
	public array $regions = [
		'Africa' => 'Africa', 'America' => 'America', 'Asia' => 'Asia', 'Australia' => 'Australia',
		'Europe' => 'Europe', 'Indian' => 'Indian', 'Pacific' => 'Pacific', 'Atlantic' => 'Atlantic',
		'Antarctica' => 'Antarctica', 'Arctic' => 'Arctic'
	];

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		$options = $this->getTimezones();

		$select = new Select($this->attributes, ['options' => $options, 'selected' => $this->selected]);
		return $select->get();
	}

	/**
	* Returns the timezone options
	* @return array
	*/
	protected function getTimezones() : array
	{
		$options = [];
		$options['UTC'] = ['UTC', false];
		$regions_array = $this->app->time->getTimezones();

		foreach ($regions_array as $region => $timezones) {
			$region_name = $this->regions[$region];
			$options[$region_name] = [$region_name, true];

			foreach ($timezones as $timezone) {
				$options[$timezone[0]] = [$timezone[1], false];
			}
		}

		return $options;
	}
}
