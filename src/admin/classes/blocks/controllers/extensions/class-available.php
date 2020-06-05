<?php
/**
* The Available Controller Class
* @author Venus-CMS
* @package Venus
*/

namespace venus\admin\blocks\controllers\extensions;

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
use function venus\is;
use function venus\to_array;
use function venus\to_item;
use venus\Document;

if (!defined('VENUS')) {
	die;
}


/**
* The Available Controller Class
* Controller used to display the 'available' screen for extension blocks
*/
abstract class Available extends Base
{

	/**
	* Builds the controller; loads the available language file
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		global $venus;
		parent::__construct($document);

		$this->view->set_layout('available');
		$this->load_language('available');

		$this->controls->set($this->document->name . '_available', $this->url, $venus->uri->add_action($this->url, 'list'));
	}

	/**
	* Returns the control's filter options to be be shown in the sidebar
	* @return array
	*/
	protected function get_filter_options() : array
	{
		return [
			'title' => ['type' => 'input', 'operator' => 'like',  'placeholder' => l('filter_title')],
			'name' => ['type' => 'input', 'operator' => 'like',  'placeholder' => l('filter_name')],
		];
	}


	/**
	* Returns the control's order options to be be shown in the sidebar
	* @return array
	*/
	protected function get_order_options() : array
	{
		return [
			'title' => ['text' => l('order_title')],
			'name' => ['text' => l('order_name')]
		];
	}

	/**
	* Lists the items items
	*/
	protected function list_items()
	{
		global $venus;
		$filter_options = $this->get_filter_options();
		$order_options = $this->get_order_options();

		$venus->plugins->run($this->prefix . 'list_items1', $filter_options, $order_options, $this);

		$items = $this->model->get_all();

		$this->controls->set_filter_options($filter_options);
		$this->controls->set_order_options($order_options);
		$items = $this->controls->build_from_array($items);

		$venus->plugins->run($this->prefix . 'list_items2', $this, $items);

		$this->model->set_data($items);
	}









	protected function _install()
	{
		global $venus;
		$venus->user->check_permission('add');

		$name = $this->request->value('name', 'file');
		if (!$name) {
			return false;
		}

		if (!$this->model->is_on_disk($name)) {
			return false;
		}
		if ($this->model->is_installed($name)) {
			return false;
		}

		///load the installer, if any
		$this->model->load_installer($name);

		$item = $this->model->fill($name);

		if (!$this->model->installer->install($item)) {
			return false;
		}

		$this->request->fill($item);

		$venus->plugins->run($this->prefix . 'install', $this, $item);

		$this->model->set_data($item);

		$this->view->render();
	}

	protected function _insert()
	{
		global $venus;
		$venus->user->check_permission('add');

		if (!$this->request->can_post(true)) {
			return false;
		}

		$name = $this->request->post('name', 'file');
		$title = $this->request->post('title');

		if (!$name) {
			return false;
		}
		if (!$this->model->is_on_disk($name)) {
			return false;
		}
		if ($this->model->is_installed($name)) {
			return false;
		}

		$venus->plugins->run($this->prefix . 'insert1', $this, $name);

		if (!$venus->ok()) {
			$this->route('install');
			return;
		}

		$id = $this->model->insert($name, $this->request->post);
		if (!$id) {
			$this->output_errors($this->model->errors->get());

			$this->route('install');
			return;
		}

		$venus->plugins->run($this->prefix . 'insert2', $this, $name, $id);

		$venus->log->log_action('install_' . $this->item_name, $title, $id);

		$this->messages->add(l($this->lang_prefix . 'installed'));

		return true;
	}

	protected function _delete()
	{
		global $venus;
		$this->set_default_methods('list');

		if (!$this->request->can_post()) {
			return false;
		}

		$names = $this->request->value_array('names', 'file');
		if (!$names) {
			return false;
		}

		$venus->user->check_permission('delete');

		$venus->plugins->run('delete1', $this, $names);

		if (!$venus->ok()) {
			return false;
		}

		$this->model->delete($names);

		$venus->log->log_action('delete_' . $this->item_name, implode(',', $names));

		$this->controls->set_page($this->document->name . '_available');

		$venus->plugins->run($this->prefix . 'delete2', $this);

		$this->messages->add(lc($names, $this->lang_prefix . 'item_delete', $this->lang_prefix . 'items_delete'));

		return true;
	}

	protected function _upload()
	{
		global $venus;
		if (!$this->request->is_uploaded_file('import_file')) {
			$this->send_error(l($this->lang_prefix . 'err_upload1'));
		}
		if (!is_writable($this->model->dir)) {
			$this->send_error(l($this->lang_prefix . 'err_upload2'));
		}

		$filename = $_FILES['import_file']['tmp_name'];
		$overwrite = $this->request->post('import_overwrite', 'i');

		$export = new \venus\admin\helpers\Export;
		$custom_headers = $export->unzip($filename);

		$type = $custom_headers['type'] ?? '';
		$name = $custom_headers['name'] ?? '';

		if ($type != $this->model->type || !$name) {
			$export->delete_zip_dir();

			$this->send_error(l($this->lang_prefix . 'err_upload3'));
		}

		$name = $venus->file->basename($name);
		$install_dir = $this->model->dir . $name;

		$venus->plugins->run($this->prefix . 'upload1', $this, $install_dir, $name);

		if (is_dir($install_dir) && !$overwrite) {
			$export->delete_zip_dir();

			$this->send_error(l($this->lang_prefix . 'err_upload4'));
		}

		if (!$export->process_zip($install_dir)) {
			$this->send_error(l($this->lang_prefix . 'err_upload5'));
		}

		$venus->plugins->run($this->prefix . 'upload2', $this, $install_dir, $name);

		$venus->log->log_action('upload_' . $this->item_name, $name);

		$venus->messages->add(l($this->lang_prefix . 'item_uploaded'));

		$this->dispatch('list');
	}
}
