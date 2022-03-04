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
	protected bool $cache = false;

	//protected	$tags_count = 0;

	/**
	* @internal
	*/
	protected static string $table = 'venus_admin_blocks';

	/**
	* @internal
	*/
	protected static string $permissions_table = 'venus_administrators_permissions';

	/**
	* @internal
	*/
	protected static string $type = 'admin_block';

	/**
	* @internal
	*/
	protected static string $namespace = "\\Cms\\Admin\\Blocks\\";

	/**
	* @see \Venus\Block::get()
	* {@inheritdoc}
	*/
	protected function get($name)
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
	* {@inheritdoc}
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareParams();

		$this->preparePermissions();

		$this->prepareTitle();

		$this->app->plugins->run('admin_block_prepare', $this);
	}

	/**
	* @see \Venus\Block::getUrl()
	* {@inheritdoc}
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

		if (!$this->app->user->id) {
			return;
		}

		$permissions = $this->app->db->select($this->getPermissionsTable(), ['user_id' => $this->app->user->id, 'block_id' => $this->id]);

		if ($permissions) {
			unset($permissions['user_id']);
			unset($permissions['block_id']);
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
	* {@inheritdoc}
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
	* {@inheritdoc}
	*/
	protected function getOutput() : string
	{
		return $this->content;
	}

	/**
	* @see \Venus\DocumentBody::setup()
	* {@inheritdoc}
	*/
	protected function setup()
	{
		$this->setApp();
		$this->setMeta();
	}

	/**
	* @see \Venus\DocumentBody::setApp()
	* {@inheritdoc}
	*/
	protected function setApp()
	{
	}

	/**
	* @see \Venus\DocumentBody::setMeta()
	* {@inheritdoc}
	*/
	protected function setMeta()
	{
		$this->app->title->set($this->title);

		$this->app->plugins->run('admin_block_set_meta', $this);

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
