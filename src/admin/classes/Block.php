<?php
/**
* The Block Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Block Class
* Object corresponding to a block extension
*/
class Block extends \Venus\Block
{
	use \Venus\Admin\Extensions\Body;

	/**
	* @var bool $cache Don't cache the content of admin blocks
	*/
	protected $cache = false;

	//protected	$tags_count = 0;

	/**
	* @internal
	*/
	protected static $table = 'venus_admin_blocks';

	/**
	* @internal
	*/
	protected static $permissions_table = 'venus_administrators_permissions';

	/**
	* @internal
	*/
	protected static $type = 'admin_block';

	/**
	* @internal
	*/
	protected static $namespace = "\\Cms\\Admin\\Extensions\\Blocks\\";

	/**
	* @see \Venus\Block::get()
	* {@inheritDoc}
	*/
	protected function get($name) : object
	{
		$table = $this->getTable();

		$sql = "
			SELECT *
			FROM {$table}
			WHERE name_crc = CRC32(:block_name) AND name = :block_name";

		$this->app->db->readQuery($sql, ['block_name' => $name]);
		$block = $this->app->db->getRow();

		return $block;
	}

	/**
	* @see \Venus\Block::prepare()
	* {@inheritDoc}
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareParams();

		$this->preparePermissions();

		$this->prepareTitle();

		$this->app->plugins->run('adminBlockPrepare', $this);
	}

	/**
	* @see \Venus\Block::getUrl()
	* {@inheritDoc}
	*/
	protected function getUrl() : string
	{
		return $this->app->uri->getAdminBlock($this->name);
	}

	/**
	* Prepares the block's permissions, based on the permissions the admin has of accessing this block
	*/
	protected function preparePermissions()
	{
		//block access to the administrators block, if the admin doesn't have explicit permissions set
		$default_no_view = false;
		if ($this->name == 'administrators' && !$this->app->user->isSuperAdmin()) {
			$default_no_view = true;
		}

		$allowed_blocks = ['index', 'login'];
		//always allow access to the index/login blocks iregardless of what permissions have been set
		if (in_array($this->name, $allowed_blocks)) {
			$this->permissions = $this->app->user->getDefaultPermissions();
			$this->app->user->setPermissions($this->permissions);
			return;
		}

		if (!$this->app->user->uid) {
			return;
		}

		$permissions = $this->app->db->selectRow($this->getPermissionsTable(), '*', ['uid' => (int)$this->app->user->uid, 'bid' => (int)$this->bid], true);

		if ($permissions) {
			unset($permissions['uid']);
			unset($permissions['bid']);
		} else {
			$permissions = $this->app->user->getDefaultPermissions();

			if ($default_no_view) {
				$permissions['view'] = 0;
			}
		}

		$this->permissions = $permissions;
		$this->app->user->setPermissions($permissions);
	}

	/**
	* @see \Venus\DocumentBody::canOutput()
	* {@inheritDoc}
	*/
	protected function canOutput() : bool
	{
		if (!$this->getId()) {
			$this->app->redirect404();
		}

		if (!$this->permissions || !$this->permissions['view']) {
			$this->app->permissionDenied();
		}

		return true;
	}

	/**
	* @see \Venus\DocumentBody::getOutput()
	* {@inheritDoc}
	*/
	protected function getOutput() : string
	{
		return $this->content;
	}

	/**
	* @see \Venus\DocumentBody::setup()
	* {@inheritDoc}
	*/
	protected function setup()
	{
		$this->setApp();
		$this->setMeta();
	}

	/**
	* @see \Venus\DocumentBody::setApp()
	* {@inheritDoc}
	*/
	protected function setApp()
	{
		$this->app->url = $this->url;
	}

	/**
	* @see \Venus\DocumentBody::setMeta()
	* {@inheritDoc}
	*/
	protected function setMeta()
	{
		$this->app->title->set($this->title);

		$this->app->plugins->run('adminBlockSetMeta', $this);

		return $this;
	}

	/**
	* The content is not cached; nothing to build
	*/
	protected function buildCache()
	{
	}

	/*************** CUSTOM FILES METHODS *****************************/
}
