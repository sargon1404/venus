<?php
/**
* The Base model for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Models\Extensions;

use venus\Item;
use venus\Controller;
use venus\admin\extensions\Info;
use venus\admin\extensions\Installer;

/**
* The Base model for admin blocks managing extensions
* Class shared by both the Available and the Listing models, for the extension blocks
*/
abstract class Base extends \Venus\Admin\Blocks\Models\Base
{
	use SharedTrait;

	/**
	* @var \venus\admin\extensions\Info The info object
	*/
	public $info = null;

	/**
	* @var \venus\admin\extensions\Installer The installer object
	*/
	protected $installer = null;

	/**
	* @var string $table The name of the table where the items are stored
	*/
	//protected static string $table = '';

	/**
	* @var string The class name of the object, if we need to load it
	*/
	//protected static string $class = '';

	/**
	* @var string The class name of the objects, if we need to load it
	*/
	//protected static string $class_objs = '';

	/**
	* @var string $id_name The name of the id column in the table
	*/
	protected static string $id_name = 'id';

	/**
	* @var string $title The name of the title column in the table
	*/
	protected static string $title_name = 'title';

	/**
	* @var string $created_by_name The name of the created_by column in the table
	*/
	protected static string $created_by_name = 'created_by';

	/**
	* @var string $root_dir The dir where the extensions are located
	*/
	protected static string $root_dir = '';

	/**
	* Builds the model
	*/
	public function __construct()
	{
		parent::__construct();

		$this->staticPropertiesExist(['table', 'root_dir']);
	}





	/**
	* Returns the root dir
	* @return string
	*/
	public function get_root_dir() : string
	{
		return static::$root_dir;
	}

	/**
	* Returns the info object of an extension
	* @return Info
	*/
	public function get_info($name) : Info
	{
		$info = new Info($this->get_root_dir(), $name);

		return $info;
	}







	/**
	* Returns the the extension's installer
	* @param string $name The name of the extension
	* @return Installer
	*/
	public function get_installer($name) : Installer
	{
		if (!isset($this->installer)) {
			$this->load_installer($name);
		}

		return $this->installer;
	}

	/**
	* Loads the extension's installer and returns it
	* @param string $name The name of the extension
	* @return object
	*/
	public function load_installer($name, $obj = null)
	{
		if (!$obj) {
			$obj = new Item;
			$obj->name = $name;
		}

		$installer_obj = new Installer($this->path, $name);
		$this->installer = $installer_obj->get_installer($obj);

		if (!$this->installer) {
			$this->installer = $this->middle->get_default_installer($obj);
		}

		$this->installer->name = $name;

		$class_name = $installer_obj->get_class();
		if (!$this->installer->name) {
			throw new \Exception("Error initializing Class {$class_name}. Was the parent constructor called?");
		}

		$this->middle->check_installer($this->installer, $class_name);

		return $this->installer;
	}

	/**
	* Returns the properties which should not be binded to the input values
	* @return array
	*/
	protected function get_bind_ignore() : array
	{
		return ['name', 'params', 'created_timestamp', 'created_by'];
	}

	/**
	* Returns the properties which should not be binded to the input values when performing a set operation
	* @return array
	*/
	protected function get_bind_ignore_set() : array
	{
		return ['name', 'unique_id', 'title', 'params', 'created_timestamp', 'created_by'];
	}

	/**
	* Custom item processing
	*/
	protected function process_item($item, $installer)
	{
		return $this->middle->process_item($item, $this->installer);
	}
}
