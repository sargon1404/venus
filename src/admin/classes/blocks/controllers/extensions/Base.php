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
	use SharedTrait;

	/**
	* Builds the controller
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		parent::__construct($document);
	}
}
