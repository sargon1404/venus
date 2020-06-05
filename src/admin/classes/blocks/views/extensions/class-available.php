<?php
/**
* The Available View Class
* @author Venus-CMS
* @package Venus
*/

namespace venus\admin\blocks\views\extensions;

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


/**
* The Available View Class
* View used to display the 'available' screen for extension blocks
*/
abstract class Available extends Base
{

	/**
	* @internal
	*/
	public $item_id = 'name';
	/**
	* @internal
	*/
	public $item_ids = 'names';


	/**
	* Returns the navbar links
	* @return array in the format [title, url, required_permission]
	*/
	protected function get_navbar_links() : array
	{
		global $venus;
		$links_array =
		[
			0 => ['url' => $this->base_url, 'permission' => 'view', 'text' => l($this->lang_prefix . 'link1')],
			1 => ['url' => $this->url, 'permission' => 'add', 'text' => l($this->lang_prefix . 'link2')]
		];

		$venus->plugins->run($this->prefix . 'get_navbar_links', $links_array, $this);

		return $links_array;
	}

	/**
	* Prepares the index page for display
	*/
	public function index()
	{
		global $venus;
		$this->navbar->set_title(l($this->lang_prefix . 'link2'), $this->lang_prefix . 'add.png');
		$this->navbar->set_history(l($this->lang_prefix . 'link2'), $this->url);
		$this->navbar->set_links($this->get_navbar_links(), 1);
		$this->navbar->set_outer_form([
			'upload' =>
			[
				'permission' => 'add', 'on_click' => "venus.admin.openUploadPopup('upload-form')",
				'redirects' => [['name' => 'back', 'icon' => 'save-back.png', 'title' => 'Save & Back'], ['name' => 'new', 'icon' => 'add.png', 'title' => 'Save & New']]
			],
			'delete' => ['permission' => 'delete', 'ajax' => true, 'tooltip' => true]
		], 'ids');

		/*$this->navbar->set_form([
			'upload' =>
			[
				'permission' => 'add', 'on_click' => "venus.admin.openUploadPopup('upload-form')",
				'redirects' => [['name' => 'back', 'icon' => 'save-back.png', 'title' => 'Save & Back'], ['name' => 'new', 'icon' => 'add.png', 'title' => 'Save & New']]
			],
			 'delete' => ['permission' => 'delete', 'ajax' => true, 'tooltip' => true]
		], 'ids');*/

		$this->prepare_items();

		$venus->plugins->run($this->prefix . 'index', $this);
	}

	/**
	* Prepares the items for display
	*/
	protected function prepare_items()
	{
		global $venus;
		$items = $this->model->get_data();

		$i = 1;
		foreach ($items as $item) {
			$item->id = $i;
			$this->prepare_item($item);

			$i++;
		}

		$this->items = $items;

		$venus->plugins->run($this->prefix . 'prepare_items', $this->items, $this);
	}

	/**
	* Prepares a single item for display
	* @param object $item The item to prepare
	*/
	//object
	protected function prepare_item($item)
	{
		global $venus;
		$id = $item->name;

		$item->install_url = $venus->uri->get_empty();
		if ($venus->user->can('add')) {
			$item->install_url = $this->get_item_url($id, 'install');
		}

		$item->actions_list = $this->get_actions_list($id, $this->get_item_actions_list($item));
		$item->actions_select = $this->get_actions_select($id, $this->get_item_actions_select($item));

		$venus->plugins->run($this->prefix . 'prepare_item', $item, $this);
	}

	/**
	* Returns the Quick Actions list which can be performed on $item
	* @param object $item The item
	* @return array
	*/
	//object
	protected function get_item_actions_list($item) : array
	{
		global $venus;
		$id = $item->name;

		$list =
		[
			'install' => ['url' => $this->get_item_url($id, 'install'), 'permission' => 'add', 'tooltip' => 'action_install'],
			'delete' => ['url' => $this->get_item_url($id, 'delete', true), 'permission' => 'delete', 'tooltip' => 'action_delete', 'ajax' => true]
		];

		$venus->plugins->run($this->prefix . 'get_item_actions_list', $item, $list, $this);

		return $list;
	}

	/**
	* Returns the Form Action options which can be performed on $item
	* @param object $item The item
	* @return array
	*/
	protected function get_item_actions_select($item) : array
	{
		global $venus;
		$options =
		[
			'install' => ['permission' => 'add','ajax' => false, 'text' => 'action_install'],
			'delete' => ['permission' => 'delete', 'ajax' => true, 'text' => 'action_delete']
		];

		$venus->plugins->run($this->prefix . 'get_item_actions_select', $this, $item, $options);

		return $options;
	}







	public function install()
	{
		global $venus;
		$this->item = $this->model->get_data();
		$this->item->id = 0;

		$this->navbar->set_links($this->get_navbar_links(), 1);
		$this->navbar->set_title(l($this->lang_prefix . 'install1'), $this->lang_prefix . 'add.png');
		$this->navbar->set_history(l($this->lang_prefix . 'install2', '{TITLE}', $this->item->title), $this->get_item_url($this->item->name, 'install'));
		$this->navbar->set_form([
			['install', 'insert'],
			['back', $this->url]
		], 'insert', $this->url);

		$this->prepare_form();

		$venus->plugins->run($this->prefix . 'install', $this, $this->item);
	}
}
