<?php
/**
* The Listing view for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Views\Extensions;

use Venus\App;

/**
* The Listing view for admin blocks managing extensions
*/
abstract class Listing extends Base
{

	/**
	* Returns the navbar links
	* @return array
	*/
	protected function getNavbarLinks()
	{
		$links = [
			'listing' =>  ['title' => App::__($this->lang_prefix . 'link1'), 'url' => $this->url, 'permission' => 'view'],
			'available' => ['title' => App::__($this->lang_prefix . 'link2'), 'url' => $this->getControllerUrl('available'), 'permission' => 'add'],
		];

		$this->plugins->run($this->prefix . 'get_navbar_links', $links, $this);

		return $links;
	}

	/**
	* Returns the navbar buttons
	* @return array
	*/
	protected function getNavbarButtons()
	{
		$buttons = [
			'enable' =>  ['permission' => 'publish', 'ajax' => true],
			'disable' =>  ['permission' => 'publish', 'ajax' => true],
			'set' =>  ['permission' => 'edit'],
			'uninstall' =>  ['permission' => 'delete', 'ajax' => true],

		];

		$this->plugins->run($this->prefix . 'get_navbar_buttons', $buttons, $this);

		return $buttons;
	}

	/**
	* The listing screen
	*/
	public function index()
	{
		$this->navbar->setTitle(App::__($this->lang_prefix . 'link1'), 'languages.png');
		$this->navbar->setHistory(App::__($this->lang_prefix . 'link1'), $this->url);
		$this->navbar->setLinks($this->getNavbarLinks(), 'listing');
		$this->navbar->setButtons($this->getNavbarButtons());

		$this->prepareItems();

		$this->plugins->run($this->prefix . 'index', $this);
	}

	/**
	* Prepares the items for display
	*/
	protected function prepareItems()
	{
		$items = $this->model->getData();

		foreach ($items as $item) {
			$this->prepareItem($item);
		}

		$this->items = $items;

		$this->plugins->run($this->prefix . 'prepare_items', $this->items, $this);
	}

	/**
	* Prepares a single item for display
	* @param object $item The item to prepare
	*/
	protected function prepareItem(object $item)
	{
		$this->app->user->addOwnItem($item->id, $item->created_by);

		$item->edit_url = 'javascript:void(0)';
		if ($this->app->user->can('edit', $item->id)) {
			$item->edit_url = $this->getItemUrl($item->id, 'edit');
		}

		$item->actions_list = $this->actions->getList($item->id, $this->getActionsListOptions($item));
		$item->actions_form = $this->actions->getForm($item->id, $this->getActionsFormOptions($item));

		$this->plugins->run($this->prefix . 'prepare_item', $item, $this);
	}

	/**
	* Returns the actions list options which can be performed on $item
	* @param object $item The item
	* @return array
	*/
	protected function getActionsListOptions($item) : array
	{
		$options = [
			'edit' => ['url' => $this->getItemUrl($item->id, 'edit'), 'permission' => 'edit', 'tooltip' => 'action_edit'],
			'status' => ['url' => $this->getItemUrl($item->id, 'disable', true), 'permission' => 'publish', 'tooltip' => 'action_disable', 'ajax' => true],
			'uninstall' => ['url' => $this->getItemUrl($item->id, 'uninstall', true), 'permission' => 'delete', 'tooltip' => 'action_uninstall', 'ajax' => true]
		];

		if (!$item->status) {
			$options['status'] = ['url' => $this->getItemUrl($item->id, 'enable', true), 'permission' => 'publish', 'tooltip' => 'action_enable', 'ajax' => true];
		}

		$this->plugins->run($this->prefix . 'get_actions_list_options', $item, $options, $this);

		return $options;
	}

	/**
	* Returns the actions form options which can be performed on $item
	* @param object $item The item
	* @return array
	*/
	protected function getActionsFormOptions($item) : array
	{
		$options = [
			'edit' => ['text' => 'action_edit', 'permission' => 'edit'],
			'export' => ['text' => 'action_export', 'permission' => 'view'],
			'status' => ['text' => 'action_disable', 'action' => 'disable', 'permission' => 'publish', 'ajax' => true],
			'uninstall' => ['text' => 'action_disable', 'permission' => 'delete', 'ajax' => true]
		];

		if (!$item->status) {
			$options['status'] = ['text' => 'action_enable', 'action' => 'enable', 'permission' => 'publish', 'ajax' => true];
		}

		$this->plugins->run($this->prefix . 'get_actions_form_options', $item, $options, $this);

		return $options;
	}






	/**
	* Returns the Quick Action options which can be performed on $item when directly editing it
	* @param object $item The item
	* @return array
	*/
	protected function get_item_quick_actions_edit2($item) : array
	{
		global $venus;
		$id = $item->id;

		$quick_actions =
		[
			['disable', $this->get_item_url($id, 'disable', true, 'quick_actions'), ['on_success' => 'venus.ui.update_status_0'], 'publish', 'action_disable'],
			['uninstall', $this->get_item_url($id, 'uninstall', true, 'index'), false, 'delete', 'action_uninstall']
		];

		if (!$item->status) {
			$quick_actions[0] = ['enable', $this->get_item_url($id, 'enable', true, 'quick_actions'), ['on_success' => 'venus.ui.update_status_1'], 'publish', 'action_enable'];
		}

		$venus->plugins->run($this->prefix . 'get_item_quick_actions_edit', $this, $item, $quick_actions);

		return $quick_actions;
	}

	public function item()
	{
		global $venus;
		$item = $this->model->get_data();

		$this->prepare_item($item);

		$this->item = $item;

		$venus->plugins->run($this->prefix . 'item', $this);
	}





	public function edit()
	{
		global $venus;
		$this->item = $this->model->get_data();
		$this->item->id = $this->item->get_id();
		$this->item->details = $this->get_details();

		$this->navbar->set_title(l($this->lang_prefix . 'edit1'), $this->lang_prefix . 'edit.png');
		$this->navbar->set_history(l($this->lang_prefix . 'edit2', '{TITLE}', $this->item->title), $this->get_item_url($this->item->id, 'edit'));
		$this->navbar->set_links($this->get_navbar_links());
		$this->navbar->set_form([
			['save', 'update'],
			['save_back', 'update'],
			['back', $this->url]
		], 'update');

		$this->prepare_form();

		$this->item->quick_action = $this->get_quick_actions($this->item->id, $this->get_item_quick_actions_edit($this->item));

		$venus->plugins->run($this->prefix . 'edit', $this, $this->item);
	}

	/**
	* Returns the details which can be displayed in the item's info box
	* @return array
	*/
	protected function get_details()
	{
		global $venus;
		$item = $this->model->get_details($this->item->id);

		$details_array =
		[
			l('details_installed_timestamp') => $this->format->timestamp($item->created_timestamp),
			l('details_installed_by') => $this->html->a($venus->uri->get_admin_user($item->created_by), $item->created_by_username, 'user'),
			l('details_modified_timestamp') => $this->format->timestamp($item->modified_timestamp),
			l('details_modified_by') => $this->html->a($venus->uri->get_admin_user($item->modified_by), $item->modified_by_username, 'user')
		];

		$venus->plugins->run($this->prefix . 'get_details', $this, $details_array);

		return $venus->ui->build_details($details_array);
	}

	public function set()
	{
		global $venus;
		$ids = $this->model->ids;
		$this->ids = implode(',', $ids);
		$this->item = $this->model->get_data();

		$venus->notifications->add(lc($ids, l($this->lang_prefix . 'set_note1'), l($this->lang_prefix . 'set_note2', '{COUNT}', count($ids))));

		$this->navbar->set_title(l($this->lang_prefix . 'set1'), $this->lang_prefix . 'edit.png');
		$this->navbar->set_links($this->get_navbar_links());
		$this->navbar->set_form([
			['save', 'update_set'],
			['save_back', 'update_set'],
			['back', $this->url]
		], 'update_set');

		$venus->plugins->run($this->prefix . 'set', $this, $this->item);
	}
}
