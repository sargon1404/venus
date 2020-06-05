<?php
/**
* The Available Languages Model Class
* @author Venus-CMS
* @package CMS\Admin\Extensions\Blocks\Languages
*/

namespace cms\admin\extensions\blocks\languages\views;

if (!defined('VENUS')) {
	die;
}


class Available extends \venus\admin\extensions\blocks\views\extensions\Available
{

	/**
	* @internal
	*/
	public $prefix = 'admin_block_languages_available';
	
	/**
	* @internal
	*/
	public string $lang_prefix = 'languages_';


	protected function prepare_item($item)
	{
		global $venus;
		$item->flag = $venus->lang->get_flag_url($item->name);

		parent::prepare_item($item);
	}
}
