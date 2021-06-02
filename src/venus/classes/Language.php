<?php
/**
* The Language Class
* @package Venus
*/

namespace Venus;

/**
* The Language Class
* Object corresponding to a language extension
*/
class Language extends \Venus\Extensions\Extension
{
	use \Mars\Language;

	/**
	* @var int $id The id of the language
	*/
	public int $id = 0;

	/**
	* @var int $parent_id The id of the parent language, if any
	*/
	public int $parent_id = 0;

	/**
	* @var string $parent_name The name of the language's parent language, if any
	*/
	public ?string $parent_name = '';

	/**
	* @var string $parent_dir The dir of the language's parent language, if any
	*/
	public string $parent_dir = '';

	/**
	* @var string|array $files Array with the keys listing the available files of the language
	*/
	public string|array $files = [];

	/**
	* @var array $parent_files Array with the keys listing the available files of the parent language
	*/
	public $parent_files = [];

	/**
	* @internal
	*/
	protected static string $table = 'venus_languages';

	/**
	* Builds the language object
	* @param int|array|object $language The language's id/data
	*/
	public function __construct(int|array|object $language = 0)
	{
		parent::__construct($language);

		$this->app->plugins->run('language_construct', $language, $this);
	}

	/**
	* Returns the strings table
	* @return string
	*/
	public function getStringsTable() : string
	{
		return static::$strings_table;
	}

	/**
	* Loads the index&errors files
	*/
	public function loadFiles()
	{
		$this->loadFile('index');
		$this->loadFile('errors');
	}

	/**
	* Returns the data from the database
	* @param int $id The language's id
	* @return object
	*/
	public function getRow(int $id) : object
	{
		$table = $this->getTable();

		$this->app->db->readQuery("
			SELECT l.*, p.name as parent_name, p.files as parent_files
			FROM {$table} as l
			LEFT JOIN {$table} as p ON l.parent_id = p.id
			WHERE l.id = {$id} AND l.status = 1");

		return $this->app->db->getRow();
	}

	/**
	* Prepares the language
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();

		$this->prepareFiles();
		$this->prepareParams();
	}

	/**
	* Prepares the base paths
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		if ($this->parent_name) {
			$this->parent_dir = $this->getDir($this->parent_name);
		}
	}

	/**
	* Prepares the files list this language has
	*/
	protected function prepareFiles()
	{
		$this->files = $this->app->serializer->unserialize($this->files);
		$this->parent_files = $this->app->serializer->unserialize($this->parent_files);
	}

	/**
	* Loads the specified $file from the languages folder
	* @param string $file The name of the file to load
	* @return $this
	*/
	public function loadFile(string $file)
	{
		if (!$file) {
			return $this;
		}

		if (in_array($file, $this->loaded_files)) {
			return $this;
		}

		$this->loaded_file[] = $file;

		$file = $file . '.php';
		$strings = [];

		if ($this->parent_id) {
			//load the file of the parent language
			if ($this->parentFileExists($file)) {
				$this->loadFilename($this->parent_dir . $file);
			}
		}

		if ($this->fileExists($file)) {
			return $this->loadFilename($this->dir . $file);
		}

		return $this;
	}

	/**
	* Returns the strings from a language file
	* @param string $file The name of the file to load
	* @return array The strings
	*/
	public function getStringsFromFile(string $file)
	{
		$file = $file . '.php';

		$strings = [];
		if ($this->parent_id) {
			if ($this->parentFileExists($file)) {
				$strings = include($this->parent_dir . $file);
			}
		}

		if ($this->fileExists($file)) {
			$strings = array_merge($strings, include($this->dir . $file));
		}

		return $strings;
	}

	/**
	* Loads the language file of an extension
	* @param string $dir The extenion's folder
	* @param string $name The extension's name
	* @param string $file The name of the file to load
	* @return bool Returns true if the extension's file was loaded from the language's files, false if the extension must load the file itself
	*/
	public function loadExtensionFile(string $dir, string $name, string $file) : bool
	{
		$loaded = false;
		$file = App::DIRS['extensions'] . '/' . App::sl($dir) . App::sl($name) . $file . '.php';

		if ($this->parent_id) {
			//check if the parent language has the file we're looking for
			if ($this->parentFileExists($file)) {
				$this->loadFilename($this->parent_dir . $file);

				$loaded = true;
			}
		}

		//check if the current language has the file
		if ($this->fileExists($file)) {
			$this->loadFilename($this->dir . $file);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	* Checks if a language file exists
	* @param string $file The filename *relative* to the language's folder
	* @return bool
	*/
	public function fileExists(string $file) : bool
	{
		if ($this->development) {
			return is_file($this->dir . $file);
		}

		if (isset($this->files[$file])) {
			return true;
		}

		return false;
	}

	/**
	* Checks if a parent language file exists
	* @param string $file The filename *relative* to the parent language's folder
	* @return bool
	*/
	public function parentFileExists(string $file) : bool
	{
		if ($this->development) {
			return is_file($this->parent_dir . $file);
		}

		if (isset($this->parent_files[$file])) {
			return true;
		}

		return false;
	}

	/**
	* Returns the files a language has
	* @return array The files
	*/
	public function getFiles() : array
	{
		$files = [];
		$this->app->file->listDir($this->dir, $dirs, $files);

		if ($files) {
			$files = array_fill_keys($files, true);
		}

		return $files;
	}
}
