<?php
/**
* The Dir Class
* @package Venus
*/

namespace Venus;

/**
* The Dir Class
* Folder Filesystem functionality
*/
class Dir extends \Mars\Dir
{
	/**
	* @see \Mars\Dir::create()
	* {@inheritdoc}
	*/
	public function create(string $dir) : bool
	{
		if (!parent::create($dir)) {
			$this->app->errors->add(App::__('dir_create_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Dir::copy()
	* {@inheritdoc}
	*/
	public function copy(string $source_dir, string $destination_dir, bool $recursive = true) : bool
	{
		if (!parent::copy($source_dir, $destination_dir, $recursive)) {
			$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Dir::moveDir()
	* {@inheritdoc}
	*/
	public function move(string $source_dir, string $destination_dir) : bool
	{
		if (!parent::move($source_dir, $destination_dir)) {
			$this->app->errors->add(App::__('dir_move_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Dir::delete()
	* {@inheritdoc}
	*/
	public function delete(string $dir, bool $recursive = true, bool $delete_dir = true, string $secure_dir = '') : bool
	{
		if (!parent::delete($dir, $recursive, $delete_dir, $secure_dir)) {
			$this->app->errors->add(App::__('dir_delete_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Dir::clean()
	* {@inheritdoc}
	*/
	public function clean(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		if (!parent::clean($dir, $recursive, $secure_dir)) {
			$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}
}
