<?php
/**
* The 'Prepare' Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Prepare Properties Trait
* Trait used by an object to prepare the title/seo/meta properties
*/
trait PrepareTrait
{
	/**
	* Prepares the object's title
	*/
	protected function prepareTitle()
	{
		$this->title_orig = $this->title;
		if (!empty($this->title_alias)) {
			$this->title = $this->title_alias;
		}
	}

	/**
	* Prepares the object's seo link properties
	*/
	protected function prepareSeo()
	{
		if (isset($this->seo_title)) {
			if (!$this->seo_title) {
				$this->seo_title = $this->title;
			}
			var_dump('html->aAttributes');
			die;
			$this->link_attributes = $this->app->html->aAttributes($this->seo_title, $this->seo_target, $this->seo_rel);
		}

		if (isset($this->seo_image_alt)) {
			if (!$this->seo_image_alt) {
				$this->seo_image_alt = $this->seo_title;
			}
		}
	}

	/**
	* Prepares the object's meta properties
	*/
	protected function prepareMeta()
	{
		if (!isset($this->meta_title)) {
			return;
		}

		if (!$this->meta_title) {
			$this->meta_title = $this->title;
		}
	}

	/**
	* Prepares the object's has* properties
	* @param array $extra_properities Extra properties to set. title/category/image will always be set along with the $extra_attributes
	*/
	protected function prepareHas(array $extra_properities = [])
	{
		$properties = ['title', 'category', 'image'];
		if ($extra_properities) {
			$properties = array_merge($properties, $extra_properities);
		}

		foreach ($properties as $prop) {
			if (!isset($this->$prop)) {
				continue;
			}

			$name = 'has_' . $prop;

			$this->$name = false;
			if ($this->$prop) {
				$this->$name = true;
			}
		}
	}
}
