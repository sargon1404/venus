<?php
/**
* The Available Languages Controller Class
* @author Venus-CMS
* @package CMS\Admin\Extensions\Blocks\Languages
*/
namespace cms\admin\extensions\blocks\languages\controllers;

use function venus\e;
use function venus\ue;
use function venus\eid;
use function venus\ee;
use function venus\l;
use function venus\el;
use function venus\ejs;
use function venus\ejsc;
use function venus\ejsl;
use function venus\sl;
use function venus\usl;

if (!defined('VENUS')) {
	die;
}


class Available extends \venus\admin\extensions\blocks\controllers\extensions\Available
{

	/**
	* @internal
	*/
	public $prefix = 'admin_block_languages_available';


	/**
	* Displays the available languages
	*/
	public function index()
	{
		global $venus;
		if (is_writable($this->model->get_root_dir())) {
			$this->warnings->add(l('languages_warning1'));
		}

		return parent::_index();
	}









	public function list()
	{
		return $this->_list();
	}

	public function install()
	{
		return $this->_install();
	}

	public function insert()
	{
		return $this->_insert();
	}

	public function delete()
	{
		return $this->_delete();
	}

	public function upload()
	{
		return $this->_upload();
	}
}
