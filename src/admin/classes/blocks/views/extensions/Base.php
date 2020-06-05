<?php
/**
* The Base view for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Views\Extensions;

use venus\Controller;

/**
* The Base view for admin blocks managing extensions
* Class shared by both the Available and the Listing views, for the extension blocks
*/
abstract class Base extends \Venus\Admin\Blocks\Views\Base
{


	/**
	* Prepares an install/edit form for display
	*/
	protected function prepare_form()
	{
		$info = $this->model->get_info($this->item->name);

		$this->item->readme = $info->get_readme();
		$this->item->license = $info->get_license();
		$this->item->description = $info->get_description($this->item->info);
		$this->item->quick_action = '';

		$this->installer = $this->model->installer;
	}
}
