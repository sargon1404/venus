<?php
/**
* The Listing controller for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Controllers\Extensions;

use Venus\Document;
use Venus\App;

/**
* The Listing controller for admin blocks managing extensions
*/
class Listing extends \Venus\Admin\Blocks\Controllers\Extensions\Base
{
	public function __construct(Document $document)
	{
		parent::__construct($document);
	}

	/**
	* Returns the control options
	* @return array
	*/
	protected function getControlOptions() : array
	{
		return [
			'filter' => [
				'title' => ['attributes' => ['placeholder' => App::__('filter_title')]],
				'name' => ['attributes' => ['placeholder' => App::__('filter_name')]],
				'status' => ['type' => 'select', 'properties' => ['options' => [ -1 => App::__('filter_status'), 1 => App::__('enabled'), 0 => App::__('disabled')]], 'filter' => 'i']
			],
			'order' => [
				'title' => ['name' => App::__('order_title')],
				'name' => ['name' => App::__('order_name')],
				'id' => ['name' => App::__('order_id')],
				'status' => ['name' => App::__('order_status')]
			],
			'default_order' => 'title'
		];
	}

	protected function _edit()
	{
		global $venus;
		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		$venus->user->check_permission('edit', $item->created_by);

		///load the installer
		$this->model->load_installer($item->name, $item);

		if (!$this->model->installer->edit($item)) {
			return false;
		}

		$this->request->fill($item);

		$venus->plugins->run($this->prefix . 'edit', $this, $item);

		$this->model->set_data($item);

		$this->view->render();
	}

	/**
	* Updates an item
	*/
	protected function _update()
	{
		global $venus;
		if (!$this->request->can_post(true)) {
			return false;
		}

		$item = $this->get_item();
		if (!$item) {
			return false;
		}

		$venus->user->check_permission('edit', $item->created_by);

		$venus->plugins->run($this->prefix . 'update1', $this, $item);

		if (!$venus->ok()) {
			$this->route('edit');
			return;
		}

		if (!$this->model->update($item, $this->request->post)) {
			$this->output_errors($this->model->errors->get());

			$this->route('edit');
			return;
		}

		$venus->plugins->run($this->prefix . 'update2', $this, $item);

		$venus->log->log_action($this->log_prefix . 'edit', $item->title, $item->get_id());

		$this->messages->add(l($this->lang_prefix . 'updated'));

		if ($venus->navbar->get_action() == 'save') {
			$this->route('edit');
		} else {
			return true;
		}
	}

	protected function _set()
	{
		global $venus;
		$ids = $this->get_ids();
		if (!$ids) {
			return false;
		}

		$venus->user->check_items_permission('edit', $ids, $this->get_table(), $this->get_id_name(), 'created_by');

		$item = $this->model->fill_set();

		$this->request->fill($item);

		$venus->plugins->run($this->prefix . 'set', $this, $ids);

		$this->model->ids = $ids;
		$this->model->set_data($item);

		$this->view->render();
	}

	/**
	* Updates multiple items as a result of a set operation
	*/
	protected function _update_set()
	{
		global $venus;
		if (!$this->request->can_post(true)) {
			return false;
		}

		$ids = $this->get_ids();
		if (!$ids) {
			return false;
		}

		$venus->user->check_items_permission('edit', $ids, $this->get_table(), $this->get_id_name(), 'created_by');

		$venus->plugins->run($this->prefix . 'set1', $this, $ids);

		if (!$venus->ok()) {
			$this->route('set');
			return;
		}

		if (!$this->model->update_set($ids, $this->request->post)) {
			$this->output_errors($this->model->errors->get());

			$this->route('set');
			return;
		}

		$venus->plugins->run($this->prefix . 'set2', $this, $ids);

		$venus->log->log_action_array($this->log_prefix . 'set', $this->get_table(), $ids, $this->get_id_name(), 'title');

		$this->messages->add(lc($ids, $this->lang_prefix . 'set_updated1', $this->lang_prefix . 'set_updated2'));

		if ($venus->navbar->get_action() == 'save') {
			$this->route('set');
		} else {
			return true;
		}
	}

	/**
	* Enable items
	*/
	protected function _enable()
	{
		global $venus;
		$route = $this->get_return_route(['quick_actions']);

		$this->set_default_methods($route);

		if (!$this->request->can_post()) {
			return false;
		}

		$ids = $this->get_ids();
		if (!$ids) {
			return false;
		}

		$venus->user->check_items_permission('publish', $ids, $this->get_table(), $this->get_id_name(), 'created_by');

		$venus->plugins->run($this->prefix . 'enable1', $this, $ids);

		if (!$venus->ok()) {
			return false;
		}

		if (!$this->model->enable($ids)) {
			$this->output_errors($this->model->errors->get());
			return false;
		}

		$venus->log->log_action_array($this->log_prefix . 'enable', $this->get_table(), $ids, $this->get_id_name(), 'title');

		$venus->plugins->run($this->prefix . 'enable2', $this, $ids);

		$this->messages->add(lc($ids, $this->lang_prefix . 'item_enable', $this->lang_prefix . 'items_enable'));

		$this->route_items($ids, $route);
	}

	/**
	* Disables items
	*/
	protected function _disable()
	{
		global $venus;
		$route = $this->get_return_route(['quick_actions']);

		$this->set_default_methods($route);

		if (!$this->request->can_post()) {
			return false;
		}

		$ids = $this->get_ids();
		if (!$ids) {
			return false;
		}

		$venus->user->check_items_permission('publish', $ids, $this->get_table(), $this->get_id_name(), 'created_by');

		$venus->plugins->run($this->prefix . 'disable1', $this, $ids);

		if (!$venus->ok()) {
			return false;
		}

		if (!$this->model->disable($ids)) {
			$this->output_errors($this->model->errors->get());
			return false;
		}

		$venus->log->log_action_array($this->log_prefix . 'disable', $this->get_table(), $ids, $this->get_id_name(), 'title');

		$venus->plugins->run($this->prefix . 'disable2', $this, $ids);

		$this->messages->add(lc($ids, $this->lang_prefix . 'item_disable', $this->lang_prefix . 'items_disable'));

		$this->route_items($ids, $route);
	}

	/**
	* Uninstalls items
	* @return array The ids of the items which have been uninstalled
	*/
	protected function _uninstall()
	{
		global $venus;
		$route = $this->get_return_route(['index']);

		$this->set_default_methods($route);

		if (!$this->request->can_post()) {
			return false;
		}

		$ids = $this->get_ids();
		if (!$ids) {
			return false;
		}

		$venus->user->check_items_permission('delete', $ids, $this->get_table(), $this->get_id_name(), 'created_by');

		$venus->plugins->run($this->prefix . 'uninstall1', $this, $ids);

		if (!$venus->ok()) {
			return false;
		}

		if (!$this->model->uninstall($ids)) {
			$this->output_errors($this->model->errors->get());
			return false;
		}

		$venus->log->log_action_array($this->log_prefix . 'uninstall', $this->get_table(), $ids, $this->get_id_name(), 'title');

		$this->controls->set_page($this->document->name);

		$venus->plugins->run($this->prefix . 'uninstall2', $this, $ids);

		$this->messages->add(lc($ids, $this->lang_prefix . 'item_uninstall', $this->lang_prefix . 'items_uninstall'));

		return true;
	}

	/**
	* Exports an item.
	* @return object The exported item
	*/
	protected function _export()
	{
		global $venus;
		if (!$this->request->check_token()) {
			return false;
		}

		$item = $this->get_item_from_ids();
		if (!$item) {
			return false;
		}

		$item_dir = $this->model->dir . $item->name;
		$header =
		[
			'name' => $item->name,
			'type' => $this->model->type
		];

		$export = new \venus\admin\helpers\Export;
		$export->set_header($header);
		$export->add_dir($item_dir);

		$venus->plugins->run($this->prefix . 'export', $this, $export);

		$venus->log->log_action($this->log_prefix . 'export', $item->name);

		if (!$export->prompt_for_download($item->name)) {
			$this->output_errors(['export']);
			return false;
		}

		die;
	}
}
