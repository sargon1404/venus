<?php
/**
* The Admin Languages Controller Class
* @package Cms\Admin\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Controllers;

use Venus\Document;

/**
* The Admin Languages Controller Class
*/
class Languages extends \Venus\Admin\Blocks\Controllers\Extensions\Listing
{
	use SharedTrait;

	/**
	* @internal
	*/
	public string $prefix = 'admin_block_languages';

	/**
	* Builds the contructor
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		parent::__construct($document);

		$this->view->setLayout('languages');
	}

	/*public function list()
	{
		return $this->_list();
	}*/

	public function item()
	{
		return $this->_item();
	}

	public function quick_actions()
	{
		return $this->_quick_actions();
	}

	public function edit()
	{
		return $this->_edit();
	}

	public function update()
	{
		return $this->_update();
	}

	public function set()
	{
		return $this->_set();
	}

	public function update_set()
	{
		return $this->_update_set();
	}

	public function enable()
	{
		return $this->_enable();
	}

	public function disable()
	{
		return $this->_disable();
	}

	public function uninstall()
	{
		return $this->_uninstall();
	}

	public function export()
	{
		return $this->_export();
	}

	public function set_default()
	{
		global $venus;
		$route = $this->get_return_route(['quick_actions']);

		$this->set_default_methods($route);

		if (!$this->request->can_post()) {
			return false;
		}

		$venus->user->check_permission('edit');

		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		if (!$this->model->set_default($item)) {
			$this->output_errors($this->model->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'set_default', $item);

		$venus->log->log_action($this->log_prefix . 'set_default', $item->title, $item->lid);

		$venus->messages->add(l('languages_item_set_default', '{TITLE}', e($item->title)), false);

		return true;
	}

	public function switch_users()
	{
		global $venus;
		$this->set_default_methods('item');

		if (!$this->request->can_post()) {
			return false;
		}

		$venus->user->check_permission('edit');

		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		if (!$this->model->switch_users($item)) {
			$this->output_errors($this->model->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'switch_users', $item);

		$venus->log->log_action($this->log_prefix . 'switch_users', $item->title, $item->lid);

		$venus->messages->add(l('languages_item_switch_users', '{TITLE}', e($item->title)), false);

		$this->send();
	}

	public function edit_strings()
	{
		global $venus;
		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		$venus->user->check_permission('edit');

		$packages_array = $this->model->get_packages($item->lid);
		if (!$packages_array) {
			return true;
		}

		$pid = $this->request->value('pid', 'i');
		if (!$pid) {
			$pid = key($packages_array);
		}

		$strings_array = $this->model->get_strings($pid);

		$venus->plugins->run($this->prefix . 'edit_strings', $pid, $item->lid, $strings_array);

		$this->model->pid = $pid;
		$this->model->lid = $item->lid;
		$this->model->item = $item;
		$this->model->packages_array = $packages_array;
		$this->model->set_data($strings_array);

		$this->view->render();
	}

	public function update_strings()
	{
		global $venus;
		if (!$this->request->can_post(true)) {
			return false;
		}

		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		$pid = $this->request->post('pid', 'i');
		$strings_array = $this->request->post_array('strings');

		if (!$pid || !$strings_array) {
			return false;
		}

		$venus->plugins->run($this->prefix . 'update_strings', $item, $pid, $strings_array);

		if (!$venus->ok()) {
			return false;
		}

		$this->model->update_strings($item->lid, $pid, $strings_array);

		$venus->log->log_action($this->log_prefix . 'update_strings', $item->title, $item->lid);

		$venus->messages->add(l($this->lang_prefix . 'edit_strings_updated'));

		if ($venus->navbar->get_action() == 'save') {
			$this->route('edit_strings');
		} else {
			return true;
		}
	}
}
