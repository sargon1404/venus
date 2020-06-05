<?php
/**
* The Admin Languages Views Class
* @package CMS\Admin\Extensions\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Views;

//use venus\admin\extensions\ExtensionInfo;

/**
* The Admin Languages Views Class
*/
class Languages extends \Venus\Admin\Blocks\Views\Extensions\Listing
{
	/**
	* @internal
	*/
	public string $prefix = 'admin_block_languages';
	
	/**
	* @internal
	*/
	public string $lang_prefix = 'languages_';

	public function get_item_quick_actions($item)
	{
		global $venus;
		$id = $item->id;

		$quick_actions = [];
		if ($item->is_default) {
			$quick_actions =
			[
				['edit', $this->get_item_url($id, 'edit'), false, 'edit', 'action_edit', true]
			];
		} else {
			$quick_actions =
			[
				['edit', $this->get_item_url($id, 'edit'), false, 'edit', 'action_edit', true],
				['disable', $this->get_item_url($id, 'disable', true), true, 'publish', 'action_disable'],
				['uninstall', $this->get_item_url($id, 'uninstall', true), true, 'delete', 'action_uninstall']
			];

			if (!$item->status) {
				$quick_actions[1] = ['enable', $this->get_item_url($id, 'enable', true), true, 'publish', 'action_enable'];
			}
		}

		$venus->plugins->run($this->prefix . 'get_item_quick_actions', $item, $quick_actions);

		return $quick_actions;
	}

	public function get_item_quick_actions_edit($item)
	{
		global $venus;
		$id = $item->id;

		$quick_actions = [];
		if (!$item->is_default) {
			$quick_actions =
			[
				['set_default', $this->get_item_url($id, 'set_default', false, 'quick_actions'), true, 'edit', 'languages_list_form2'],
				['disable', $this->get_item_url($id, 'disable', true, 'quick_actions'), ['on_success' => 'venus.ui.update_status_0'], 'publish', 'action_disable'],
				['uninstall', $this->get_item_url($id, 'uninstall', true, 'index'), false, 'delete', 'action_uninstall']
			];

			if (!$item->status) {
				$quick_actions[1] = ['enable', $this->get_item_url($id, 'enable', true, 'quick_actions'), ['on_success' => 'venus.ui.update_status_1'], 'publish', 'action_enable'];
			}
		}

		$venus->plugins->run($this->prefix . 'get_item_quick_actions_edit', $item, $quick_actions);

		return $quick_actions;
	}

	public function get_item_form_actions($item)
	{
		global $venus;
		$id = $item->id;

		$form_options = [];
		if ($item->is_default) {
			$form_options =
			[
				['edit', false, 'edit', 'action_edit'],
				['edit_strings', false, 'edit', 'languages_list_form1'],
				['switch_users', true, 'edit', 'languages_list_form3'],
				['export', false, 'view', 'action_export'],
			];
		} else {
			$form_options =
			[
				['edit', false, 'edit', 'action_edit'],
				['edit_strings', false, 'edit', 'languages_list_form1'],
				['set_default', true, 'edit', 'languages_list_form2'],
				['switch_users', true, 'edit', 'languages_list_form3'],
				['export', false, 'view', 'action_export'],
				['disable', true, 'publish', 'action_disable'],
				['uninstall', true, 'delete', 'action_uninstall']
			];

			if (!$item->status) {
				$form_options[5] = ['enable', true, 'publish', 'action_enable'];
			}
		}

		$venus->plugins->run($this->prefix . 'get_item_form_action', $item, $form_options);

		return $form_options;
	}

	public function edit_strings()
	{
		global $venus;
		$chars = 70;

		$this->pid = $this->model->pid;
		$this->item = $this->model->item;
		$this->package_name = $this->model->packages_array[$this->model->pid];
		$this->packages = $this->model->packages_array;

		$this->navbar->set_title(l($this->lang_prefix . 'edit_strings1'), $this->lang_prefix . 'edit.png');
		$this->navbar->set_history(l($this->lang_prefix . 'edit_strings2', '{TITLE}', $this->item->title), $this->get_item_url($this->item->lid, 'edit_strings'));
		$this->navbar->set_links($this->get_navbar_links());
		$this->navbar->set_form([
			['save', 'update_strings'],
			['save_back', 'update_strings'],
			['back', $this->url]
		], 'update_strings', $this->url, '', false, false);

		$strings = $this->model->get_data();
		foreach ($strings as $i => $string) {
			$string->big = false;
			if (strlen($string->text) > $chars) {
				$string->big = true;
			}
		}

		$this->strings = $strings;

		$venus->plugins->run($this->prefix . 'edit_strings', $this);
	}
}
