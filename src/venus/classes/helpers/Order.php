<?php
/**
* The Order Class
* @package Venus
*/

namespace Venus\Helpers;

use Venus\App;

/**
* The Order Class
* Items ordering class
*/
class Order
{
	use \Venus\AppTrait;

	/**
	* @var string $ajax_update_element The default javascript update element, when making ajax calls
	*/
	protected $ajax_update_element = '';

	/**
	* @var array $icons Array storing the generated order icons
	*/
	protected $icons = [];

	/**
	* @var array $icons Array storing the generated order links
	*/
	protected $links = [];

	/**
	* Builds up/down icons depending on the $orderby and $order fields.
	* @param array $order_options Array with the order options. Must be in the format order_by_field => order_by_db_field
	* @param string $orderby The orderby field as defined in $order_options
	* @param string $order The order type: asc/desc
	* @return array The generated icons
	*/
	public function buildIcons(array $order_options, string $orderby, string $order) : array
	{
		$this->icons = [];

		if (!$order_options) {
			return [];
		}

		$order = strtolower($order);

		foreach ($order_options as $field => $db_field) {
			if ($field == $orderby) {
				if ($order == 'desc') {
					$this->icons[$field] = $this->app->html->img($this->app->theme->images_url . 'arrow_down.png', 0, 0, '', '', 'order-link-selected');
				} else {
					$this->icons[$field] = $this->app->html->img($this->app->theme->images_url . 'arrow_up.png', 0, 0, '', '', 'order-link-selected');
				}
			} else {
				$this->icons[$field] = '';
			}
		}

		return $this->icons;
	}

	/**
	* Outputs an order icon
	* @param string $field The name of the field for which the icon will be outputed
	*/
	public function outputIcon(string $field)
	{
		echo $this->icons[$field];
	}

	/**
	* Builds order links by appending the necesarilly params to $base_url
	* @param string $base_url The base url to which the order params will be appended to
	* @param array $order_options Array with the fields defining the valid order fields
	* @param string $orderby The value of the current order by field. If empty will be determined by a call to $this->app->request->getOrderBy()
	* @param string $order The value of the current order field. If empty will be determined by a call to $this->app->request->getOrder()
	* @param string $orderby_param The orderby param. If empty $this->app->config->orderby_param is used
	* @param string $order_param The orderby param. If empty $this->app->config->order_param is used
	* @param string $use_ajax If true, when ordering the pages, the calls will be made using ajax
	* @param string $js_update_element_id The DOM element on top the loading icon is displayed, if any
	* @param string $js_function javascript function to be called when changing the page via ajax. If empty no function is used and the content of <block> are automatically updated
	* @return array The generated order links
	*/
	public function buildLinks(string $base_url, array $order_options, string $orderby = '', string $order = '', string $orderby_param = '', string $order_param = '', bool $use_ajax = false, string $js_update_element_id = '', string $js_function = '')  : array
	{
		$this->links = [];

		if (!$order_options) {
			return [];
		}

		if (!$orderby_param) {
			$orderby_param = $this->app->config->orderby_param;
		}
		if (!$order_param) {
			$order_param = $this->app->config->order_param;
		}
		if (!$orderby) {
			$orderby = $this->app->request->getOrderBy();
		}
		if (!$order) {
			$order = $this->app->request->getOrder();
		}

		$order = strtolower($order);

		foreach ($order_options as $field => $dbfield) {
			$porder = 'desc';
			if ($field == $orderby) {
				if ($order == 'desc') {
					$porder = 'asc';
				}
			}

			$this->links[$field] = $this->getLink($base_url, $orderby_param, $field, $order_param, $porder, $use_ajax, $js_update_element_id, $js_function);
		}

		return $this->links;
	}

	/**
	* Returns the url of an order link
	* @internal
	*/
	protected function getLink(string $base_url, string $orderby_param, string $field, string $order_param, string $porder, bool $use_ajax, string $js_update_element_id, string $js_function) : string
	{
		if ($use_ajax) {
			$url = App::ejs($this->app->uri->build($base_url, [$orderby_param => $field, $order_param => $porder, $this->app->config->response_param => 'ajax']));

			if (!$js_update_element_id) {
				$js_update_element_id = $this->ajax_update_element;
			}

			$js_field = App::ejs($field);
			$js_order = App::ejs($porder);
			$js_update_element_id = App::ejs($js_update_element_id);

			return "javascript:venus.ui.order_update('{$url}', '{$js_field}', '{$js_order}', '{$js_update_element_id}', '{$js_function}');";
		} else {
			return e($this->app->uri->build($base_url, [$orderby_param => $field, $order_param => $porder]));
		}
	}

	/**
	* Builds ajx order links by appending the necesarilly params to $base_url
	* @param string $base_url The base url to which the order params will be appended to
	* @param array $order_options Array with the fields defining the valid order fields
	* @param string $orderby The value of the current order by field. If empty will be determined by a call to $this->app->request->getOrderBy()
	* @param string $order The value of the current order field. If empty will be determined by a call to $this->app->request->getOrder()
	* @param string $js_update_element_id The DOM element on top the loading icon is displayed, if any
	* @param string $js_function javascript function to be called when changing the page via ajax. If empty no function is used and the content of <block> are automatically updated
	* @param string $orderby_param The orderby param. If empty $this->app->config->orderby_param is used
	* @param string $order_param The orderby param. If empty $this->app->config->order_param is used
	* @return array The generated order links
	*/
	public function buildAjaxLinks(string $base_url, array $order_options, string $orderby = '', string $order = '', string $js_update_element_id = '', string $js_function = '', string $orderby_param = '', string $order_param = '') : array
	{
		return $this->buildLinks($base_url, $order_options, $orderby, $order, $orderby_param, $order_param, true, $js_update_element_id, $js_function);
	}

	/**
	* Outputs an order link
	* @param string $field The name of the field for which the link will be outputed
	*/
	public function outputLink(string $field)
	{
		echo $this->links[$field];
	}
}
