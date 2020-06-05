<?php
/**
* The Usergroup Class
* @package Venus
*/

namespace Venus;

/**
* The Usergroup Class
* Encapsulates the functionality of an usergroup
*/
class Usergroup extends Item
{
	/**
	* @internal
	*/
	protected static string $table = 'venus_usergroups';

	/**
	* Returns the data of an usergroup
	* @param int $id The usergroup's id
	* @return object The usergroup
	*/
	public function getRow(int $id) : ?object
	{
		return $this->app->env->getUsergroup($id);
	}

	/**
	* Returns the avatar url of an usergroup
	* @param string $avatar_type The image type: image/thumb/small_thumb
	* @return string The avatar's url
	*/
	public function getAvatarUrl(string $avatar_type = 'image') : string
	{
		if ($avatar_type == 'image') {
			$avatar_type = '';
		}
		if ($avatar_type) {
			$avatar_type = $avatar_type . '_';
		}
		var_dump('to_do');
		die;
		if ($this->avatar) {
			$images_url = $this->app->images_url;
			if ($this->app->theme->type == 't') {
				if (!$this->avatar_for_tablets) {
					$images_url = VENUS_IMAGES_DIR;
				}
			} elseif ($this->app->theme->type == 's') {
				if (!$this->avatar_for_smartphones) {
					$images_url = VENUS_IMAGES_DIR;
				}
			}

			return $images_url . 'avatars/' . $avatar_type . basename(eurl_filename($this->avatar));
		} else {
			return $this->app->theme->images_url . $avatar_type . 'avatar.png';
		}
	}

	/**
	* Returns the avatar's width
	* @param string $avatar_type The type of the image [image/thumb/small_thumb]
	* @return int The width of the avatar
	*/
	public function getAvatarWidth(string $avatar_type = 'image') : int
	{
		if (!$avatar_type) {
			$avatar_type = 'image';
		}

		return $this->app->theme->getImageWidth('avatar', $avatar_type);
	}

	/**
	* Returns the avatar's height
	* @param string $avatar_type The type of the image [image/thumb/small_thumb]
	* @return int The height of the avatar
	*/
	public function getAvatarHeight(string $avatar_type = 'image') : int
	{
		if (!$avatar_type) {
			$avatar_type = 'image';
		}

		return $this->app->theme->getImageHeight('avatar', $avatar_type);
	}
}
