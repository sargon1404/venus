<?php
/**
* The Category Trait
* @package Venus
*/

namespace Venus\Document;

use Venus\Content\Category;

/**
* The Category Trait
* Loads the primary category the object belongs to.
* The protected static $category_image_type_table property must be declared in the classes using this trait
* @property string $category_image_type The category's image type: image/thumb/small_thumb
*/
trait CategoryTrait
{

	/**
	* @var string $category_image_type The category's image type: image/thumb/small_thumb
	*/
	//protected static $category_image_type = 'small_thumb';

	/**
	* Prepares the category proprties of an object
	*/
	protected function prepareCategory()
	{
		if (!isset($this->category)) {
			return;
		}

		$this->category = new Category($this->category);
		if (!$this->category->cid) {
			$this->category->loadDefaults();
		}

		if (static::$category_image_type != $this->category->getImageType()) {
			$this->category->setImageType(static::$category_image_type);
		}
	}
}
