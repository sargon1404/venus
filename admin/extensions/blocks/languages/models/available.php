<?php
/**
* The Available Languages Model Class
* @author Venus-CMS
* @package CMS\Admin\Extensions\Blocks\Languages
*/
namespace cms\admin\extensions\blocks\languages\models;

//use \venus\admin\blocks\models\extensions\Middle;

if (!defined('VENUS')) {
	die;
}


class Available extends \venus\admin\extensions\blocks\models\extensions\Available
{

	/**
	* @internal
	*/
	public $prefix = 'admin_block_languages_available';


	protected function get_fill_data($name, $vars)
	{
		return $this->db->fill($this->get_table(), [
			'name' => $name, 'status' => 1, 'note' => '', 'debug' => 0,
			'content' => 1,
			'encoding' => ifset($vars, 'encoding', 'UTF-8'),
			'code' => ifset($vars, 'code', 'en'),
			'url_code' => ifset($vars, 'url_code', 'en'),
			'accept_code' => ifset($vars, 'accept_code', 'en'),
			'title_lang' => ifset($vars, 'title', ''),
			'timestamp_format' => ifset($vars, 'timestamp_format', 'D M d, Y g:i a'),
			'date_format' => ifset($vars, 'date_format', 'D M d, Y'),
			'time_format' => ifset($vars, 'time_format', 'g:i a'),
			'birthday_format' => ifset($vars, 'birthday_format', 'F d Y'),
			'date_picker_format' => ifset($vars, 'date_picker_format', 'mm/dd/yyyy'),
			'time_picker_format' => ifset($vars, 'time_picker_format', 'hh:mm:ss'),
			'decimal_separator' => ifset($vars, 'decimal_separator', '.'),
			'thousands_separator' => ifset($vars, 'thousands_separator', ',')
		], -1);
	}
}
