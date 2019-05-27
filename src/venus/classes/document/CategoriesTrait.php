<?php
/**
* The Categories Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Categories Trait
* Loads the categoris the object belongs to.
* The protected static $_categories_table property must be declared in the classes using this trait
* @property string $_categories_table The database table storing the object's categories
*/
trait CategoriesTrait
{
	/**
	* @var array $categories The categories to which the object belongs to
	*/
	public $categories = null;

	/**
	* @internal
	*/
	protected static $categories_table = 'venus_items_categories';

	/**
	* Returns the name of the categories table
	* @return string
	*/
	public function getCategoriesTable() : string
	{
		return static::$categories_table;
	}

	/**
	* Inits the categories of the object
	*/
	protected function prepareCategories()
	{
		$this->categories = $this->getCategories();
	}

	/**
	* Loads and returns the categories the object belongs to
	* @return array The categories
	*/
	public function getCategories() : array
	{
		if ($this->categories !== null) {
			return $this->categories;
		}

		if ($this->all_categories) {
			$this->categories = $this->app->env->getCategoryIds();
		} else {
			$this->categories = $this->app->db->selectField($this->getCategoriesTable(), 'cid', ['id' => $this->getId(), 'type' => $this->getType()]);
		}

		return $this->categories;
	}
}
