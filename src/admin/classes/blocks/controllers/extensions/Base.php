<?php
/**
* The Base controller for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Controllers\Extensions;

use Venus\Document;

/**
* The Base controller for admin blocks managing extensions
* Class shared by both the Available and the Listing controllers
*/
abstract class Base extends \Venus\Admin\Blocks\Controllers\Base
{
	/**
	* @var Middle $middle Middle object, with functionality shared by the Listing and Available controllers
	*/
	protected Middle $middle;

	/**
	* @var array $middle_properties Array with the properties to copy from the middle controller
	*/
	protected array $middle_properties = ['item_name', 'items_name', 'lang_prefix'];

	/**
	* Builds the controller
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		parent::__construct($document);

		$this->middle = $this->getMiddleController();

		$this->copyMiddleControllerProperties();
	}

	/**
	* Returns the middle controller
	* @return Middle The middle controller
	*/
	protected function getMiddleController() : Middle
	{
		$class_name = $this->getClassNamespace() . '\\Middle';

		return new $class_name;
	}

	/**
	* Sets the required properties defined in the middle controller
	*/
	protected function copyMiddleControllerProperties()
	{
		foreach ($this->middle_properties as $prop) {
			if (isset($this->middle->$prop)) {
				$this->$prop = $this->middle->$prop;
			}
		}

		$this->log_prefix = $this->item_name . '_';
	}

	/**
	* Outputs the errors, if any. Should be overriden in the middle controller
	* @param array $errors The errors
	*/
	protected function outputErrors(array $errors)
	{
		return $this->middle->outputErrors($errors);
	}
}
