<?php
/**
* The Admin Languages Controller Shared Trait
* @package Cms\Admin\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Controllers;

/**
* The Admin Languages Controller Shared Trait
*/
trait SharedTrait
{
	/**
	* @internal
	*/
	public string $item_name = 'language';

	/**
	* @internal
	*/
	public string $items_name = 'languages';

	/**
	* @internal
	*/
	public string $lang_prefix = 'languages_';

	/**
	* @internal
	*/
	public string $log_prefix = 'languages_';


	/**
	* Outputs the errors
	* @param array $errors The errors to output
	*/
	public function outputErrors(array $errors)
	{
		var_dump($errors);
		die;
		$strings =
		[
			'title' => l('languages_err101'),
			'export' => l('languages_err201'),
			'default_disable' => l('languages_err202'),
			'default_uninstall' => l('languages_err203'),
			'disabled_set_default' => l('languages_err204'),
			'disabled_switch_users' => l('languages_err205')
		];

		$venus->errors->add(get_strings($errors, $strings));
	}
}
