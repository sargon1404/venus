<?php
/**
* The Admin Languages Model Shared Trait
* @package Cms\Admin\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Models;

use venus\admin\extensions\Installer;
use venus\admin\installers\LanguageInstaller;

/**
* The Admin Languages Model Shared Trait
*/
trait SharedTrait
{
	/**
	* @internal
	*/
	protected array $properties = [
		'table' => 'venus_languages',
		'root_dir' => 'languages'
	];

	public function get_default_installer($item = null) : Installer
	{
		var_dump("xcvcx");
		die;
		return new LanguageInstaller($item);
	}

	public function check_installer($installer, $class_name)
	{
		if (!$installer instanceof LanguageInstaller) {
			throw new \Exception("Class {$class_name} must extend class \\venus\\admin\\installers\\LanguageInstaller");
		}
	}

	public function process_item($item, $installer)
	{
		$item->cached_packages = serialize_field($installer->get_cached_packages());
		$item->set_packages($installer->get_packages());

		return $item;
	}
}
