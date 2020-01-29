<?php
/**
* The Moderator Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Moderator Trait
* Determines if the current user is a moderator of this item. Will set up the moderator permissions
*/
trait ModeratorTrait
{
	/**
	* @var bool $is_moderator True if the user is a moderator of this item
	*/
	public bool $is_moderator = false;

	/**
	* @var array $moderator_permissions The moderator permissions of the user, if he's a moderator
	*/
	public array $moderator_permissions = ['publish' => 0, 'edit' => 0, 'delete' => 0];

	/**
	* Marks the user as a moderator of the document
	*/
	protected function markAsModerator()
	{
		$this->is_moderator = true;

		$this->app->lang->loadFile('moderator');
	}

	/**
	* Sets the moderator permissions, if the user is a moderator of this document
	*/
	protected function prepareModerator()
	{
		if (!$this->app->user->is_moderator) {
			return;
		}

		$type = $this->getType();
		$id = $this->getId();

		//if the user is an admin, grant him all moderator permissions automatically
		if ($this->app->user->is_admin) {
			$this->moderator_permissions = ['edit' => 1, 'publish' => 1, 'delete' => 1];

			$this->markAsModerator();

			return;
		}

		if (isset($this->app->user->moderator_permissions[$type][$id])) {
			//the user is a moderator of this document
			$this->moderator_permissions = $this->app->user->moderator_permissions[$type][$id];

			if ($this->moderator_permissions) {
				$this->markAsModerator();
			}

			return;
		}

		//check if the user is a moderator of a category the document belongs to
		if (!$this->categories) {
			return;
		}

		$is_moderator = false;
		foreach ($this->categories as $cid) {
			if (!isset($this->app->user->moderator_permissions['category'][$cid])) {
				continue;
			}

			$perm = $this->app->user->moderator_permissions['category'][$cid];
			foreach ($perm as $name => $val) {
				if ($val) {
					$this->moderator_permissions[$name] = $val;
					$is_moderator = true;
				}
			}
		}

		if ($is_moderator) {
			$this->markAsModerator();
		}
	}
}
