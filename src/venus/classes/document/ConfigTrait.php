<?php
/**
* The Config Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Config Trait
* Prepares the config options of an object.
* The protected static $config_prefix property must be declared in the classes using this trait
* @property string $config_prefix The config prefix
*/
trait ConfigTrait
{

	/**
	* @var string $config_prefix The config prefix. Must be defined in classes using this trait
	*/
	/*protected static $config_prefix = '';*/

	/**
	* Sets the config options
	* @param array $config_array Array listing the config options to set
	* @param string $value The value the config option must have to be set
	*/
	protected function setConfig(array $config_array, string $value = '-1')
	{
		foreach ($config_array as $name) {
			if ($this->$name == $value) {
				$this->$name = $this->app->config->get(static::$config_prefix . $name);
			}
		}
	}
}
