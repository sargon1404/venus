<?php
/**
* The Base controller for admin blocks
* @package Venus
*/

namespace Venus\Admin\Blocks\Controllers;

use Venus\Document;

/**
* Base Controller Class for admin controllers
*/
abstract class Base extends \Venus\Admin\Controller
{
	/**
	* @var array $control_options The control options
	*/
	public array $control_options = ['filter' => [], 'order' => [], 'default_order' => ''];

	/**
	* @var string $item_name The name of the item. Used when logging etc..
	*/
	public string $item_name = '';

	/**
	* @var string $items_name The name of the items (plural). Used when logging etc..
	*/
	public string $items_name = '';

	/**
	* @var string $lang_prefix Prefix to be used when using language strings
	*/
	public string $lang_prefix = '';

	/**
	* @var string $log_prefix Prefix to be used when logging
	*/
	public string $log_prefix = '';

	/**
	* Builds the controller
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		parent::__construct($document);

		$this->model = $this->getModel();
		$this->view = $this->getView();

		$this->control_options = $this->getControlOptions();
	}

	/**
	* Returns the control options
	* @return array
	*/
	protected function getControlOptions() : array
	{
		return ['filter' => [], 'order' => [], 'default_order' => ''];
	}


	/**
	* Returns an item, from the id param
	* @return object
	*/
	protected function get_item()
	{
		global $venus;
		$id = $this->request->value('id', 'id');

		return $this->_get_item($id);
	}

	/**
	* Returns an item, from the ids param
	* @return object The item or null
	*/
	protected function get_item_from_ids()// : ?object
	{
		global $venus;
		$id = $this->request->value('ids', 'id');

		return $this->_get_item($id);
	}

	/**
	* Returns an item from the id
	* @param int $id The id
	* @return object The item or null
	*/
	protected function _get_item(int $id)// : ?object
	{
		if (!$id) {
			return null;
		}

		$item = $this->model->get($id);

		if (!$item->is()) {
			return null;
		}

		return $item;
	}

	/**
	* Return the ids of the items to be processed.
	* @return array Array containing the ids
	*/
	protected function get_ids() : array
	{
		return $this->request->value('ids', 'ids');
	}

	/**
	* Returns the return route
	* @param array $allowed_route List of allowable routes
	* @param string $default_route The default return route
	* @param string $default_ajax_route The default ajax return route
	* @return string The return route, if any
	*/
	protected function get_return_route(array $allowed_routes = [], string $default_route = 'index', string $default_ajax_route = 'list') : string
	{
		global $venus;
		if ($venus->response->is_ajax()) {
			$default_route = $default_ajax_route;
		}

		return $this->request->get_return_route($allowed_routes, $default_route);
	}

	/**
	* Routes to the list or item actions
	* @param array $ids The item ids
	* @param string $route The route
	*/
	protected function route_items(array $ids, string $route)
	{
		global $venus;
		if ($route == 'list') {
			if (count($ids) == 1) {
				$route = 'item';
			}
		}

		$this->route($route);
	}

	/**
	* The index action. Lists the available items
	*/
	public function index()
	{
		$this->listItems();

		$this->plugins->run($this->prefix . 'index', $this);

		$this->view->render();
	}

	public function list()
	{
		$this->listItems();

		$this->plugins->run($this->prefix . 'list', $this);

		$this->view->send();
	}

	/**
	* Lists the items
	*/
	protected function listItems()
	{
		$this->plugins->run($this->prefix . 'list_items1', $this);

		$this->controls->set($this->prefix, $this->uri->addAction($this->url, 'list'), $this->getControlOptions());

		$controls_data = $this->controls->get();

		$this->plugins->run($this->prefix . 'list_items2', $controls_data, $this);

		$this->model->loadByData($controls_data);
	}











	protected function _item()
	{
		global $venus;
		$id = $this->request->value('ids', 'id');

		$item = $this->_get_item($id);
		if (!$item) {
			return false;
		}

		$venus->plugins->run($this->prefix . 'item', $this, $item);

		$this->model->set_data($item);

		$data = $this->get_item_response_data($id);

		$this->view->send($data);
	}

	protected function get_item_response_data($id)
	{
		$response = new \venus\framework\response\Ajax;
		$data = $response->get();

		$data['element'] = "item-{$id}";

		return $data;
	}

	protected function _quick_actions()
	{
		global $venus;
		$item = $this->get_item_from_ids();
		if (!$item) {
			$item = $this->get_item();
		}
		if (!$item) {
			return;
		}

		$item->id = $item->get_id();

		$venus->plugins->run($this->prefix . 'item_quick_action', $this, $item);

		$data = ['element' => 'quick-action'];

		$this->send($this->view->item_edit_quick_actions($item), $data);
	}
}
