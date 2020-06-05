<?php
/**
* The Languages Middle Model
* @package CMS\Admin\Extensions\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Models;

use venus\admin\extensions\Installer;
use venus\admin\installers\LanguageInstaller;

/**
* The Languages Middle Model
*/
class Middle extends \Venus\Admin\Blocks\Models\Extensions\Middle
{
	/**
	* @internal
	*/
	public string $table = 'venus_languages';

	/**
	* @internal
	*/
	public string $root_dir = '';
	//public string $root_dir = VENUS_LANGUAGES_DIR;


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
