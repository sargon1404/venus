<?php
/**
* The Controls Class
* @package Venus
*/

namespace Venus\Helpers;

use Venus\App;

/**
* The Controls Class
* Manages the filter/order/items per page/pagination controls
*/
class Controls
{
	use \Venus\AppTrait;

	/**
	* @var string $session The session key where the session data is stored
	*/
	protected string $session_key = 'controls';

	/**
	* @var string $session The name under which the controls values will be saved in the session
	*/
	protected string $session = '';

	/**
	* @var string $url The url of the controls form
	*/
	protected string $base_url = '';

	/**
	* @var array $filter_array Array storing the filters list
	*/
	protected array $filter_array = [];

	/**
	* @var array $order_array Array storing the orders list
	*/
	protected array $order_array = [];

	/**
	* @var string $order_default_value The default order value to be returned, if no order control is selected
	*/
	protected string $order_default_value = '';



	/**
	*	@var bool $build_filters_html If true, will built the html for the filter controls. If the filters are not needed, should be set to false
	*/
	public bool $build_filters_html = true;

	/**
	* @var bool $build_filters_ajax If true, will use ajax when filtering
	*/
	public bool $build_filters_ajax = true;

	/**
	*	@var bool $build_order_html If true, will built the html for the order controls. If the order controls are not needed, should be set to false
	*/
	public bool $build_order_html = true;

	/**
	*	@var bool $build_items_per_page_html If true, will built the 'items per page' control
	*/
	public bool $build_items_per_page_html = true;

	/**
	*	@var bool $build_pagination_html If true, will built the pagination
	*/
	public bool $build_pagination_html = true;

	/**
	* @var string $filter_ignore_value If the filter value equals $filter_ignore_value, that filter will not be included in the sql. Used to indicate select top options/hints
	*/
	public string $filter_ignore_value = '-1';


	/**
	* @var string $filter_javascript_function javascript function to be executed when the filter button is clicked
	*/
	public string $filter_javascript_function = 'venus.controls.filter()';

	/**
	* @var string $filter_reset_javascript_function javascript function to be executed when the filter reset button is clicked
	*/
	public string $filter_reset_javascript_function = 'venus.controls.filter_reset()';

	/**
	* @var array $order_skip Array with the order options which will be shown; but skipped when returning the sql code
	*/
	public array $order_skip = [];

	/**
	* @var bool $order_links If true, order links will be built based on control's data
	*/
	public bool $order_links = true;

	/**
	* @var bool $order_links_use_ajax If true, the order links will use ajax for ordering results
	*/
	public bool $order_links_use_ajax = true;

	/**
	* @var string $order_show_reset_button If true will show the reset button
	*/
	public bool $order_show_reset_button = true;

	/**
	* @var string $order_javascript_function javascript function to be executed when the order button is clicked
	*/
	public string $order_javascript_function = 'venus.controls.order()';

	/**
	* @var string $order_reset_javascript_function javascript function to be executed when the order reset button is clicked
	*/
	public string $order_reset_javascript_function = 'venus.controls.order_reset()';

	/**
	* @var int $items_per_page The default number of items per page
	*/
	public int $items_per_page = 0;

	/**
	* @var string $items_per_page_button The text of the items per page button
	*/
	public string $items_per_page_button = '';

	/**
	* @var string $items_per_page_javascript_function javascript function to be executed when the Display button is clicked
	*/
	public string $items_per_page_javascript_function = 'venus.controls.items_per_page()';

	/**
	* @var bool $pagination_use_ajax If true,the pagination will use ajax for changing pages
	*/
	public bool $pagination_use_ajax = true;

	/**
	* @var bool $pagination_use_ajax If true,the pagination will use ajax for changing pages
	*/
	public bool $pagination_is_dialog = false;

	/**
	* @var int $total_items The determined total items
	*/
	public int $total_items = 0;





	/**
	* @var string $pagination_table The database table from where the total items for the pagination are read
	*/
	protected string $pagination_table = '';

	/**
	* @var array $pagination_joins Array with joins to add to the pagination's sql code
	*/
	protected array $pagination_joins = [];

	/**
	* @var int $pagination_items_count The total number of items. Instead of specifying the pagination table/joins, the total items can be set instead
	*/
	protected int $pagination_items_count = 0;

	/**
	* @var string $controls_filters The generated filters html code is stored here
	*/
	protected string $controls_filters = '';

	/**
	* @var string $controls_order The generated orders html code is stored here
	*/
	protected string $controls_order = '';

	/**
	* @var string $controls_items_per_page The generated items per page html code is stored here
	*/
	protected string $controls_items_per_page = '';

	/**
	* @var string $controls_pagination The generated pagination html code is stored here
	*/
	protected string $controls_pagination = '';

	/**
	* @internal
	*/
	protected bool $filter_saved = false;

	/**
	* @internal
	*/
	protected bool $order_saved = false;

	/**
	* @internal
	*/
	protected bool $bottom_saved = false;

	/**
	* Builds the controls object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		$this->items_per_page = $this->app->config->items_per_page;

		$this->session_key = $this->app->session->getPrefix() . $this->session_key;

		$this->sessionInit();
	}

	/***************SESSION METHODS**********************************/

	/**
	* Inits the session data used by the controls
	*/
	protected function sessionInit()
	{
		if (!isset($_SESSION[$this->session_key])) {
			$_SESSION[$this->session_key] = [];
		}
	}

	/**
	* Inits the session data used by the controls
	*/
	protected function sessionInitKey()
	{
		if (!isset($_SESSION[$this->session_key][$this->session])) {
			$_SESSION[$this->session_key][$this->session] = [];
		}
	}

	/**
	* Returns the session data
	* @param mixed $not_set The value to return if the session isn't set
	* @return mixed The data
	*/
	protected function sessionGetAll($not_set = [])
	{
		if (!isset($_SESSION[$this->session_key][$this->session])) {
			return $not_set;
		}

		return $_SESSION[$this->session_key][$this->session];
	}

	/**
	* Merges the session values with the options
	* @param array $options The options
	*/
	protected function sessionMergeAll(array $options)
	{
		$_SESSION[$this->session_key][$this->session] = $options + $_SESSION[$this->session_key][$this->session];
	}

	/**
	* Determines if the a field is set in the session
	* @param string $name The name of the field
	* @return bool
	*/
	protected function sessionIsSet(string $name) : bool
	{
		return isset($_SESSION[$this->session_key][$this->session][$name]);
	}

	/**
	* Returns the value of a session field
	* @param string $name The name of the field
	* @param mixed $not_set The value to return if the session isn't set
	* @return mixed
	*/
	protected function sessionGet(string $name, $not_set = [])
	{
		if (!isset($_SESSION[$this->session_key][$this->session][$name])) {
			return $not_set;
		}

		return $_SESSION[$this->session_key][$this->session][$name];
	}

	/**
	* Sets the value of a field is set in the session
	* @param string $name The name of the field
	* @param string $value The value
	*/
	protected function sessionSet(string $name, string $value)
	{
		$_SESSION[$this->session_key][$this->session][$name] = $value;
	}

	/***************SET METHODS**********************************/

	/**
	* Returns the control data
	* @return array
	*/
	public function get() : array
	{
		$data = [
			'where' => $this->getWhere()
		];

		return $data;
	}

	/**
	* Sets the filter & order options
	* @param string $session Unique identifier of the page where the controls will be displayed
	* @param string $url The url of the controls form
	* @param array $options The control options
	* @return $this
	*/
	public function set(string $session, string $url, array $options)
	{
		$this->session = $session;
		$this->url = $url;

		$this->filter_array = $options['filter'] ?? [];
		$this->order_array = $options['order'] ?? [];
		$this->order_default_value = $options['default_order'] ?? '';

		$this->sessionInitKey();

		return $this;
	}

	/**
	* Sets the filter options
	* Each element of $filter_array must be in the format: filter_name => [...params]
	* The supported params are:
	* 'type' => The filter's type [input | select | hidden | select_page | select_user]
	* 'column' => The database column corresponding to this filter. If not specified, the name/key of the filter is used
	* 'value' => The filter's default value
	* 'operator' => The filter type [=, !=, >, <, >=, <= ,like]
	* 'placeholder' => Placeholder text for the input filters
	* 'options' => Options for the select filter [array]
	* 'filter' => Filtering to be applied on the value. Eg: i|f (int, float)

	* 0 => database_field = The database field/column corresponding to this filter
	* 5 => having_field  If specified, will return the filter in a HAVING claused rather than in WHERE. Useful, if custom sql code must be speficied

	* @param array $filter_array Array defining the filter options
	* @return $this
	*/
	public function setFilterOptions(array $filter_array)
	{
		$this->filter_array = $filter_array;

		return $this;
	}

	/**
	* Sets the order options
	* Each element of $order_array must be in the format: name => [...params]
	* The supported params are:
	* 'column' => The database column corresponding to this order option. If not specified, the name/key of the option is used
	* 'text' => The text of the order option
	* 'invert' => If true, the entries in the db are considered to be in reverse order
	* @param array $order_array Array defining the order options
	* @param string $order_default_value The default order value to be returned, if no order control is selected
	* @return $this
	*/
	public function setOrderOptions(array $order_array, string $order_default_value = '')
	{
		$this->order_array = $order_array;
		$this->order_default_value = $order_default_value;

		return $this;
	}




	/**
	* Instead of specifying the pagination table/joins, the total items can be set instead
	* @param int $pagination_items_count The total number of items
	* @return $this
	*/
	public function setPaginationItems(int $pagination_items_count)
	{
		$this->pagination_items_count = $pagination_items_count;

		return $this;
	}

	/**
	* Returns the WHERE conditions of the controls
	* @return array
	*/
	protected function getWhere() : array
	{
		if ($this->filter_array) {
			return [];
		}

		$where_array = [];

		foreach ($this->filter_array as $name => $field) {
			if (!$this->sessionIsSet($name)) {
				continue;
			}
			die("oooo");
		}
		die;
		if ($this->filter_array) {
			$sql_array = [];

			foreach ($this->filter_array as $name => $field) {
				if (in_array($name, $this->filter_skip)) {
					continue;
				}
				if (!$this->sessionIsSet($name)) {
					continue;
				}
				if (!empty($field[5])) {
					continue;
				}

				$value = $this->getValue($name);
				$db_field = $field[0];
				$comparison_type = $field[1];
				$filter_type = $field[2];
				$value_filter_type = $field[4] ?? '';

				if ($filter_type == 'input') {
					if (!$value) {
						continue;
					}
				}

				$sql_array[$db_field] = [$this->app->filter->value($value, $value_filter_type), $comparison_type];
			}
			var_dump($sql_array);
			die;
			$where_sql = trim($this->app->db->getWhere($sql_array, $this->filter_ignore_value, true, $this->filter_delimitator));
		}

		if (trim($this->filter_extra_where)) {
			if ($where_sql) {
				$where_sql.= $this->filter_delimitator . $this->filter_extra_where;
			} else {
				$where_sql = 'WHERE ' . $this->filter_extra_where;
			}
		}

		return $where_sql;
	}

	/**
	* Builds the controls and returns the sql code
	* @param bool $build_top_controls If true, will build the "top" controls (filters and orders)
	* @param bool $build_bottom_controls If true, will build the "bottom" controls (items per page and pagination)
	* @param bool $build_order_links If true, will build the order links
	* @param bool $build_sql If true, will build the sql code
	* @return string The generated sql code
	*/
	public function build(bool $build_top_controls = true, bool $build_bottom_controls = true, bool $build_order_links = true, bool $build_sql = true) : string
	{
		if (!$this->session) {
			return '';
		}

		//save&update the options
		$this->saveTopOptions();
		$this->saveBottomOptions();
		$this->updateOptions();

		//build the order links and the top controls
		if ($build_order_links) {
			$this->buildOrderLinks();
		}
		if ($build_top_controls) {
			$this->buildControlsTop();
		}
		die("nnnn");
		if ($build_sql) {
			$where_sql = $this->getWhereSql();
			$having_sql = $this->getHavingSql($having_fields);

			//now that we have the $where_sql and $having_sql compute the total items
			$this->total_items = $this->computeTotalItems($where_sql, $having_sql, $having_fields);

			$order_sql = $this->getOrderSql();
			$limit_sql = $this->getLimitSql();

			$this->where_sql = ' ' . $where_sql;
			$this->having_sql = ' ' . $having_sql;
			$this->order_sql = ' ' . $order_sql;
			$this->limit_sql = ' ' . $limit_sql;
			$this->order_limit_sql = ' ' . $this->order_sql . $this->limit_sql;
			$this->sql = ' ' . $where_sql . ' ' . $having_sql . ' ' . $order_sql . ' ' . $limit_sql;
		}

		//build the items per page & pagination controls
		if ($build_bottom_controls) {
			$this->buildControlsBottom();
		}

		return $this->sql;
	}

	/**
	* Returns the generated sql called
	* @return string
	*/
	public function getSql() : string
	{
		return $this->sql;
	}

	/********************VARS METHODS************************************************/

	/**
	* Gets a control value
	* @param string $name The name of the control
	* @param mixed $not_set Value to return if no control value is set
	* @return string  The control's value
	*/
	public function getValue(string $name, $not_set = null) : ?string
	{
		if (!$this->session) {
			return $not_set;
		}

		return $this->sessionGet($name, $not_set);
	}

	/**
	* Sets a control value
	* @param string $name The name of the control
	* @param string $value The value
	* @return $this
	*/
	public function setValue(string $name, string $value)
	{
		if (!$this->session) {
			return;
		}

		$this->sessionSet($name, $value);

		return $this;
	}

	/**
	* Returns the current control vars (items per page/current page/orderby/order)
	* @return array The control vars
	*/
	public function getVars() : array
	{
		$items_per_page = (int)$this->getValue('items_per_page');
		if (!$items_per_page || $items_per_page < 0) {
			$items_per_page = $this->items_per_page;
		}

		$page = (int)$this->getValue('page');
		if (!$page || $page < 0) {
			$page = 1;
		}

		return [
			'items_per_page' => $items_per_page,
			'page' => $page,
			'orderby' => (string)$this->getValue('orderby'),
			'order' => (string)$this->getValue('order')
		];
	}

	/**
	* Returns the raw current control vars
	* @return array The raw control vars
	*/
	public function getRawVars() : array
	{
		if (!$this->session) {
			return [];
		}

		return $this->sessionGetAll();
	}

	/**
	* Sets the page of the control options. Should be set to 0 after operations which inserts/delets items.
	* @param int $page The page. Defaults to 0
	* @return $this
	*/
	public function setPage(int $page = 0)
	{
		if (!$page || $page < 0) {
			$page = 1;
		}

		$this->setValue('page', $page);

		return $this;
	}

	/**
	* Returns the current page number
	* @return int The page number
	*/
	public function getPage() : int
	{
		$vars = $this->getVars();

		return $vars['page'];
	}

	/**
	* Sets the number of items per page
	* @param int $items_per_page The number of items per page
	* @return $this
	*/
	public function setItemsPerPage(int $items_per_page = 0)
	{
		if (!$items_per_page) {
			$items_per_page = $this->items_per_page;
		}

		$this->setValue('items_per_page', $items_per_page);

		return $this;
	}

	/**
	* Returns the current number or items per page
	* @return int The items per page number
	*/
	public function getItemsPerPage() : int
	{
		$vars = $this->getVars();

		return $vars['items_per_page'];
	}

	/**
	* Returns the number of total items which are to be shown
	* @return int
	*/
	public function getTotalItems() : int
	{
		return $this->total_items;
	}

	/**
	* Returns the filter values
	* @return array
	*/
	public function getFilterVars() : array
	{
		if (!$this->filter_array) {
			return [];
		}

		$vars = [];
		foreach ($this->filter_array as $name => $filter) {
			$vars[$name] = $this->getValue($name);
		}

		return $vars;
	}

	/**
	* Determines if the filter var named $name exists and doesn not equal the default value
	* @param string $name The name of the filter var
	* @return bool
	*/
	public function isFilterVar(string $name) : bool
	{
		if (!isset($this->filter_array[$name])) {
			return false;
		}

		$value = $this->getValue($name);
		if ($value === null || $value == $this->filter_ignore_value) {
			return false;
		}

		return true;
	}

	/**
	* Returns the filtered value of a filter var.
	* @param string $name The name of the filter var
	* @return string The value
	*/
	public function getFilterVar(string $name) : string
	{
		if (!isset($this->filter_array[$name])) {
			return null;
		}

		$value = $this->getValue($name);
		$filter = $this->filter_array[$name];
		$filter_type = $filter['filter'] ?? '';

		return $this->app->filter->value($value, $filter_type);
	}

	/******************SAVE METHODS********************************/

	/**
	* Saves the posted filter&order control options
	* @return $this
	*/
	public function saveTopOptions()
	{
		$this->saveFilterOptions();
		$this->saveOrderOptions();

		return $this;
	}

	/**
	* Saves the posted filter control options
	* @return $this
	*/
	public function saveFilterOptions()
	{
		if (!$this->session || $this->filter_saved) {
			return $this;
		}

		$action = $this->app->request->value('controls_action');
		if ($action != 'save_filter') {
			return $this;
		}

		$this->app->request->checkToken(false);

		$options = $this->getVars();

		//read the filter values
		$filter_values = $this->app->request->valueArray('controls_filter');
		if ($filter_values) {
			$options = array_merge($options, $filter_values);
		}

		if ($this->app->request->isPost('reset') || $this->app->request->isPost('controls-filter-reset-button')) {
			//the reset button was presset. Reset the filter options to their default values
			$options = $this->resetFilterOptions($options);
		}

		$this->saveSessionOptions($options);

		$this->filter_saved = true;

		//reset the page
		$this->setPage();

		return $this;
	}

	/**
	* Saves the posted order control options
	* @return $this
	*/
	public function saveOrderOptions()
	{
		if (!$this->session || $this->order_saved) {
			return $this;
		}

		$action = $this->app->request->value('controls_action');
		if ($action != 'save_order') {
			return $this;
		}

		$this->app->request->checkToken(false);

		$options = [
			'orderby' => $this->app->request->getOrderBy(),
			'order' => $this->app->request->getOrder()
		];

		$this->saveSessionOptions($options);

		$this->order_saved = true;

		return $this;
	}

	/**
	* Saves the options in the session
	* @param array $options The options
	*/
	protected function saveSessionOptions(array $options)
	{
		$this->sessionMergeAll($options);
	}

	/**
	* Resets the filter options to their default values
	* @param array $options The options [out]
	* @return array The options
	*/
	protected function resetFilterOptions(array &$options) : array
	{
		if (!$this->filter_array) {
			return $options;
		}

		foreach ($this->filter_array as $name => $filter) {
			$value = '';
			$filter['type'] = $filter['type'] ?? '';

			if ($filter_type == 'select') {
				if (isset($filter[6])) {
					$value = $filter[6];
				} else {
					if (isset($filter[3])) {
						$options_array = array_keys($filter[3]);
						$value = reset($options_array);
					}
				}
			} elseif ($filter_type == 'hidden') {
				if (isset($filter[3])) {
					$value = $filter[3];
				}
			} elseif ($filter_type == 'select_user') {
				$options[$name . '_uid'] = 0;
			} elseif ($filter_type == 'select_page') {
				$options[$name . '_pid'] = 0;
			}

			if (isset($options[$name])) {
				$options[$name] = $value;
			}
		}

		return $options;
	}

	/**
	* Saves the posted bottom control options
	* @return $this
	*/
	public function saveBottomOptions()
	{
		if (!$this->session || $this->bottom_saved) {
			return $this;
		}

		$action = $this->app->request->value('controls_action');
		if ($action != 'save_bottom') {
			return $this;
		}

		$this->app->request->checkToken(false);

		$this->sessionSet('items_per_page', $this->app->request->value('controls_items_per_page', 'i'));

		$this->setPage();

		$this->bottom_saved = true;

		return $this;
	}

	/**
	* Update the control options using the request data
	*/
	protected function updateOptions()
	{
		if (!$this->session) {
			return;
		}

		//set the order/order by, if it's has been specified in the GET query
		$orderby = $this->app->request->getOrderBy();
		$order = $this->app->request->getOrder();

		if ($orderby) {
			$this->sessionSet('orderby', $orderby);
		}
		if ($order) {
			$this->sessionSet('order', $order);
		}

		//set the page, if it's has been specified in the GET query
		$page_param = $this->app->config->page_param;
		if ($this->app->request->isGet($page_param)) {
			$page = $this->app->request->get($page_param, 'i');

			$this->sessionSet('page', $page);
		} elseif ($this->getValue('page')) {
			$this->app->request->get[$page_param] = (int)$this->getValue('page');
		}
	}

	/********************FILTER METHODS*******************************************/

	/**
	* Builds the filter controls
	*/
	protected function buildFilters() : string
	{
		if (!$this->filter_array) {
			return '';
		}

		$html = '';

		//build the first line of filters
		foreach ($this->filter_array as $name => $filter) {
			$html.= '<div class="controls-filter-field">';
			$html.= $this->buildFilterField($name, $filter);
			$html.= '</div>';
		}

		$onclick_filter = '';
		$onclick_reset = '';

		if ($this->build_filters_ajax) {
			$onclick_filter = ' onclick="' . $this->filter_javascript_function . '; return false;"';
			$onclick_reset = ' onclick="' . $this->filter_reset_javascript_function . '; return false;"';
		}

		//output the filter submit button, the reset button, the expand button & html
		$html.= '<div id="controls-filters-buttons">';
		$html.= '<div id="controls-filters-action">';
		$html.= '<input type="submit" name="controls-filter-button" id="controls-filter-button" value="' . App::e($this->filter_button) . '"' . $onclick_filter . ' />';

		if ($this->filter_show_reset_button) {
			$html.= '<input type="submit" name="controls-filter-reset-button" id="controls-filter-reset-button" class="reset" value="' . App::e($this->filter_reset_button) . '"' . $onclick_reset . ' />';
		}

		$html.= '</div>';
		$html.= '<div class="loading-small" id="controls-filter-loading"></div>';
		$html.= '<div class="clear"></div>';

		$html.= '</div>' . "\n";

		return $html;
	}

	/**
	* Builds a filter field
	* @param string $name The name of the field
	* @param array $filter The filter's data
	* @return string The field's html code
	*/
	protected function buildFilterField(string $name, array $filter) : string
	{
		$filter['type'] = $filter['type'] ?? '';
		$filter['placeholder'] = $filter['placeholder'] ?? '';

		$value = $this->getFilterValue($this->getValue($name), $filter);

		switch ($filter['type']) {
			case 'select':
				return $this->app->html->select('controls_filter[' . $name . ']', $filter['options'], $value);

			case 'hidden':
				return $this->app->html->requestHidden('controls_filter[' . $name . ']', $value, 'controls_filter[' . $name . ']');

			case 'select_page':
				$value = $this->getFilterValue($this->getValue($name . '_pid'), $filter);

				return $this->app->html->selectPage('controls_filter[' . $name . ']', $value, false, $filter['placeholder'], true, 'select-page', 'controls_filter[' . $name . '_pid]');

			case 'select_user':
				return $this->app->html->selectUser('controls_filter[' . $name . ']', $value, false, $filter['placeholder'], true, 'select-user', 'controls_filter[' . $name . '_uid]');

			case 'input':
			default:
				return $this->app->html->request('controls_filter[' . $name . ']', $value, false, $filter['placeholder']);
		}
	}

	/**
	* Returns a filter's value
	* @param string $value The value
	* @param array $filter The filter's data
	* @return string
	*/
	protected function getFilterValue(?string $value, array $filter) : string
	{
		if ($value === null && isset($filter['value'])) {
			$value = $filter['value'];
		}

		return (string)$value;
	}

	/**********************ORDER METHODS*******************************************/

	/**
	* Builds the order controls
	*/
	protected function buildOrders() : string
	{
		if (!$this->order_array) {
			return '';
		}

		$orderby_param = $this->app->config->orderby_param;
		$order_param = $this->app->config->order_param;
		$orderby_options = ['' => App::__('controls_orderby_select')];

		foreach ($this->order_array as $name => $option) {
			$orderby_options[$name] = $option['text'];
		}

		$order_options = [
			'' => App::__('controls_order_select'),
			'asc' => App::__('controls_order_asc'),
			'desc' => App::__('controls_order_desc')
		];

		$orderby = $this->app->html->select($orderby_param, $orderby_options, (string)$this->getValue('orderby'));
		$order = $this->app->html->select($order_param, $order_options, (string)$this->getValue('order'));

		$onclick_order = '';
		$onclick_reset = '';

		if ($this->build_filters_ajax) {
			$onclick_order = ' onclick="' . $this->order_javascript_function . '; return false;"';
			$onclick_reset = ' onclick="' . $this->order_reset_javascript_function . '; return false;"';
		}

		$html = '<div class="controls-order-field">' . $orderby . '</div>';
		$html.= '<div class="controls-order-field">' . $order . '</div>';

		$html.= '<div id="controls-order-buttons">';
		$html.= '<div id="controls-order-action">';
		$html.= '<input type="submit" name="controls-order-button" id="controls-order-button" value="' . App::e($this->order_button) . '"' . $onclick_order . '  />';

		if ($this->order_show_reset_button) {
			$html.= '<input type="submit" name="controls-order-reset-button" id="controls-order-reset-button" class="reset" value="' . App::e($this->order_reset_button) . '"' . $onclick_reset . ' />';
		}

		$html.= '</div>';
		$html.= '<div class="loading-small" id="controls-order-loading"></div>';
		$html.= '<div class="clear"></div>';

		$html.= '</div>' . "\n";

		return $html;
	}

	/********************SQL GENERATING METHODS***********************************/

	/**
	* Returns the while sql clause coresponding to the filter options
	* @return string The sql code
	*/
	protected function getWhereSql() : string
	{
		var_dump("get where sql");
		die;
		$where_sql = '';
		var_dump($this->filter_array);
		die;
		if ($this->filter_array) {
			$sql_array = [];

			foreach ($this->filter_array as $name => $field) {
				if (in_array($name, $this->filter_skip)) {
					continue;
				}
				if (!$this->sessionIsSet($name)) {
					continue;
				}
				if (!empty($field[5])) {
					continue;
				}

				$value = $this->getValue($name);
				$db_field = $field[0];
				$comparison_type = $field[1];
				$filter_type = $field[2];
				$value_filter_type = $field[4] ?? '';

				if ($filter_type == 'input') {
					if (!$value) {
						continue;
					}
				}

				$sql_array[$db_field] = [$this->app->filter->value($value, $value_filter_type), $comparison_type];
			}
			var_dump($sql_array);
			die;
			$where_sql = trim($this->app->db->getWhere($sql_array, $this->filter_ignore_value, true, $this->filter_delimitator));
		}

		if (trim($this->filter_extra_where)) {
			if ($where_sql) {
				$where_sql.= $this->filter_delimitator . $this->filter_extra_where;
			} else {
				$where_sql = 'WHERE ' . $this->filter_extra_where;
			}
		}

		return $where_sql;
	}

	/**
	* Returns the having sql clause coresponding to the filter options
	* @param string $having_fields The having fields are written in this variable [out]
	* @return string The sql code
	*/
	protected function getHavingSql(string &$having_fields = '') : string
	{
		var_dump("oooo");
		die;
		return '';
		if (!$this->filter_array) {
			return '';
		}

		$sql_array = [];
		$fields_array = [];

		foreach ($this->filter_array as $name => $field) {
			if (in_array($name, $this->filter_skip)) {
				continue;
			}
			if (!$this->sessionIsSet($name)) {
				continue;
			}
			if (empty($field[5])) {
				continue;
			}

			$value = $this->getValue($name);
			$db_field = $field[0];
			$comparison_type = $field[1];
			$filter_type = $field[2];
			$value_filter_type = $field[4] ?? '';

			if ($filter_type == 'input') {
				if (!$value) {
					continue;
				}
			}

			$sql_array[$db_field] = [$this->app->filter->value($value, $value_filter_type), $comparison_type];

			$fields_array[] = $field[5];
		}

		if ($fields_array) {
			$having_fields = ', ' . implode(', ', $fields_array);
		}

		return trim($this->app->db->getHaving($sql_array, $this->filter_ignore_value, true, $this->filter_delimitator));
	}

	/**
	* Returns the 'order by' sql query coresponding to the order options
	* @return string The sql code
	*/
	protected function getOrderSql() : string
	{
		$orderby = $this->getValue('orderby');
		$order = $this->getValue('order');

		if (!isset($this->order_array[$orderby])) {
			return $this->app->db->sql->reset()->orderBy($this->order_default_value)->getSql();
		}

		$option = $this->order_array[$orderby];
		$option['column'] = 	$option['column'] ?? $orderby;
		$option['invert'] = $option['invert'] ?? false;

		if ($option['invert']) {
			//invert order
			if ($order == 'ASC') {
				$order = 'DESC';
			} else {
				$order = 'ASC';
			}
		}

		return $this->app->db->sql->reset()->orderBy($option['column'], $order)->getSql();
	}

	/**
	* Returns the limit sql based on how many items per page should be displayed
	* @return string The sql code
	*/
	protected function getLimitSql() : string
	{
		if (!$this->total_items) {
			return '';
		}

		$page = $this->getPage();
		$items_per_page = $this->getItemsPerPage();

		//check the requested page is not greater than the max number of pages
		$max_pages = ceil($this->total_items / $items_per_page);
		if ($max_pages < $page) {
			$page = 1;
		}

		return $this->app->db->getPageLimit($page, $items_per_page, $this->total_items);
	}

	/**
	* Returns the total items
	* @param string $where_sql The where sql
	* @param string $having_sql The having sql
	* @param string $having_fields Having fields
	* @return int The total items count
	*/
	protected function computeTotalItems(string $where_sql, string $having_sql, string $having_fields) : int
	{
		if ($this->pagination_items_count) {
			return $this->pagination_items_count;
		}

		if (!$this->pagination_table) {
			return 0;
		}

		//if we have joins specified, include it when building the count sql
		$joins_sql = '';
		if ($this->pagination_joins) {
			$pagination_joins_array = [];
			$filter_vars = $this->getFilterVars();

			foreach ($this->pagination_joins as $filter_name => $join) {
				if (!isset($this->filter_array[$filter_name])) {
					$pagination_joins_array[] = $join[0];
					continue;
				}

				$val = $filter_vars[$filter_name];
				if ($val === null) {
					continue;
				}

				$join_sql = $join[0];
				$ignore_value = $join[2];
				$filter_type = $join[3] ?? '';

				if ($join[1]) {
					if ($ignore_value == $val) {
						continue;
					}

					$join_sql = str_replace('{VALUE}', $this->app->filter->value($val, $filter_type), $join_sql);
				}

				$pagination_joins_array[] = $join_sql;
			}

			if ($pagination_joins_array) {
				$joins_sql = ' ' . implode(' ', $pagination_joins_array) . ' ';
			}
		}

		$total_items = 0;
		if ($having_sql) {
			$table = $this->pagination_table;
			$having_fields = trim(trim($having_fields), ',');

			$this->app->db->readQuery("SELECT COUNT(*) AS count FROM ( SELECT {$having_fields} FROM {$table} {$joins_sql} {$where_sql} {$having_sql}) AS c");

			$total_items = $this->app->db->getResult();
		} else {
			$total_items = $this->app->db->count($this->pagination_table . $joins_sql, $where_sql);
		}

		return $total_items;
	}

	/*************************ARRAY METHODS**************************************/

	/**
	* Builds the controls from an array
	* @param array $array The array
	* @return array
	*/
	public function buildFromArray(array $array) : array
	{
		//build the bottom controls [items per page and pagination] manually, once we determined how many items we have after we filtered it
		$this->build(true, false, true, false);

		$array = $this->filterArray($array);
		$array = $this->orderArray($array);

		$this->total_items = count($array);

		$this->buildControlsBottom();

		$array = $this->splitArray($array);

		return $array;
	}

	/**
	* Filters an array, based on the filter options
	* @param array $array The array to filter
	* @return The filtered array
	*/
	public function filterArray(array $array) : array
	{
		if (!$this->filter_array) {
			return $array;
		}

		foreach ($this->filter_array as $name => $filter) {
			if (in_array($name, $this->filter_skip)) {
				continue;
			}
			if (!$this->sessionIsSet($name)) {
				continue;
			}

			$filter['type'] = $filter['type'] ?? 'input';
			$filter['column'] = $filter['column'] ?? $name;
			$filter['operator'] = $filter['operator'] ?? 'like';
			$value = $this->getValue($name);

			if ($value == $this->filter_ignore_value) {
				continue;
			}

			if ($filter['type'] == 'input') {
				if (!$value) {
					continue;
				}
			}

			$array = array_filter($array, function ($arr) use ($value, $filter) {
				$key = $filter['column'];

				if (!isset($arr[$key])) {
					return true;
				}

				$compare_value = $arr[$key];

				return $this->keepFilterValue($value, $compare_value, $filter['operator']);
			});
		}

		return $array;
	}

	/**
	* Determines if a filter value must be kept
	* @param string $value The value
	* @param string $compare_value The value to compare $value with
	* @param string $operator The operator. Eg: =, !=, >, <, like
	* @return bool
	*/
	protected function keepFilterValue(string $value, string $compare_value, string $operator = '') : bool
	{
		$value = strtolower($value);
		$operator = trim(strtolower($operator));
		$compare_value = trim(strtolower($compare_value));

		switch ($operator) {
			case '=':
				return $value == $compare_value;
			case '!=':
				return $value != $compare_value;
			case '>':
				return $value > $compare_value;
			case '>=':
				return $value >= $compare_value;
			case '<':
				return $value < $compare_value;
			case '<=':
				return $value <= $compare_value;
			case 'like':
				$value = preg_quote($value, '/');

				return preg_match("/{$value}/isU", $compare_value, $m);
		}

		return true;
	}

	/**
	* Orders an array, based on the order options
	* @param array $array The array to order
	* @return The ordered array
	*/
	public function orderArray(array $array) : array
	{
		$orderby = $this->getValue('orderby');
		$order = $this->getValue('order');

		if (!$orderby && !$order) {
			return $array;
		}

		if (!$orderby && $order == 'desc') {
			return array_reverse($array, true);
		}

		if (!isset($this->order_array[$orderby])) {
			return $array;
		}

		$option = $this->order_array[$orderby];
		$option['column'] = $option['column'] ?? $orderby;

		uasort($array, function ($a, $b) use ($option, $order) {
			$index = $option['column'];

			$res = strcmp($a[$index], $b[$index]);
			if ($res == 0) {
				return 0;
			} elseif ($res > 0) {
				if ($order == 'desc') {
					return -1;
				} else {
					return 1;
				}
			} else {
				if ($order == 'desc') {
					return 1;
				} else {
					return -1;
				}
			}
		});

		return $array;
	}

	/**
	* Splits an array, based on the pagination options and returns the chunk
	* @param array $array The array to split
	* @return The chunk corresponding to the pagination options
	*/
	public function splitArray(array $array) : array
	{
		$total_items = count($array);
		$page = $this->getPage();

		$items_per_page = $this->getItemsPerPage();

		//check the requested page is not greater than the max number of pages
		$max_pages = ceil($total_items  / $items_per_page);
		if ($max_pages < $page) {
			$page = 1;
		}

		$offset = ($page - 1) * $items_per_page;

		return array_slice($array, $offset, $items_per_page);
	}

	/*********************BUILD METHODS******************************************/

	/**
	* Builds the order links
	*/
	protected function buildOrderLinks()
	{
		if (!$this->order_links) {
			return;
		}

		$vars = $this->getVars();

		$this->app->order->buildIcons($this->order_array, $vars['orderby'], $vars['order']);

		if ($this->order_links_use_ajax) {
			$this->app->order->buildAjaxLinks($this->app->uri->appendPage($this->ajax_url, $vars['page']), $this->order_array, $vars['orderby'], $vars['order']);
		} else {
			$this->app->order->buildLinks($this->app->uri->appendPage($this->ajax_url, $vars['page']), $this->order_array, $vars['orderby'], $vars['order']);
		}
	}

	/**
	* Builds the top controls: filter and order options
	*/
	protected function buildControlsTop()
	{
		if ($this->build_filters_html && $this->filter_array) {
			$url = $this->base_url;
			$onsubmit = '';

			if ($this->build_filters_ajax) {
				$url = $this->ajax_url;
				$onsubmit = ' onsubmit="' . $this->filter_javascript_function . '; return false"';
			}

			$filters = '<form action="' . App::e($url) . '" method="post" id="controls-filters-form"' . $onsubmit . '>';
			$filters.= '<input type="hidden" name="controls_action" value="save_filter" />';
			$filters.= $this->app->html->getToken();
			if ($this->build_filters_ajax) {
				$filters.= $this->app->html->getAjax();
			}

			$filters.= $this->buildFilters();
			$filters.= '</form>';

			$this->controls_filters = $filters;
		}

		if ($this->build_order_html && $this->order_array) {
			$url = $this->base_url;
			$onsubmit = '';

			if ($this->build_filters_ajax) {
				$url = $this->ajax_url;
				$onsubmit = ' onsubmit="' . $this->order_javascript_function . '; return false"';
			}

			$order = '<form action="' . App::e($url) . '" method="post" id="controls-order-form"' . $onsubmit . '>';
			$order.= '<input type="hidden" name="controls_action" value="save_order" />';
			$order.= $this->app->html->getToken();
			if ($this->build_filters_ajax) {
				$order.= $this->app->html->getAjax();
			}

			$order.= $this->buildOrders();

			$order.= '</form>';

			$this->controls_order = $order;
		}
	}

	/**
	* Builds the bottom controls: items per page and pagination
	*/
	protected function buildControlsBottom()
	{
		$items_per_page = $this->getItemsPerPage();
		if ($this->build_items_per_page_html) {
			$url = $this->base_url;
			$onsubmit = '';

			if ($this->build_filters_ajax) {
				$url = $this->ajax_url;
				$onsubmit = ' onsubmit="' . $this->items_per_page_javascript_function . '; return false"';
			}

			$items = '<div id="controls-items-per-page">';
			$items.= '<form action="' . App::e($url) . '" method="post" id="controls-items-per-page-form"' . $onsubmit . '>';
			$items.= '<div id="controls-items-per-page-action">';
			$items.= '<input type="hidden" name="controls_action" value="save_bottom" />';
			$items.= $this->app->html->getToken();
			$items.= '<input type="text" name="controls_items_per_page" value="' . App::e($items_per_page) . '" class="small" />';
			$items.= '&nbsp;<input type="submit" value="' . App::e($this->items_per_page_button) . '" />';
			$items.= '</div>';
			$items.= '<div class="loading-small" id="controls-items-per-page-loading"></div>';
			$items.= '<div class="clear"></div>';
			$items.= '</form>';
			$items.= '</div>';

			$this->controls_items_per_page = $items;
		}

		if ($this->build_pagination_html) {
			$pagination = '<div id="controls-pagination">';
			if ($this->total_items) {
				$pagination.= $this->buildPagination();
			}
			$pagination.= '</div>';

			$this->controls_pagination = $pagination;
		}
	}

	/**
	* Builds the pagination based on filter options and the current page
	*/
	protected function buildPagination()
	{
		$items_per_page = $this->getItemsPerPage();

		if ($this->pagination_use_ajax) {
			return $this->app->ui->buildAjaxPagination($this->ajax_url, $this->total_items, $items_per_page, '', '', '', '', false, '', $this->pagination_is_dialog);
		} else {
			return $this->app->ui->buildPagination($this->ajax_url, $this->total_items, $items_per_page);
		}
	}

	/****************OUTPUT METHODS***********************************/

	/**
	* Outputs the top [filter and orders] controls
	*/
	public function outputTop()
	{
		if (!$this->controls_filters && !$this->controls_order) {
			return;
		}

		echo '<div id="controls-top">';
		$this->outputFilters();
		$this->outputOrder();
		echo '</div>';
	}

	/**
	* Outputs the bottom [items_per_page and pagination] controls
	*/
	public function outputBottom()
	{
		if (!$this->controls_items_per_page && !$this->controls_pagination) {
			return;
		}

		echo '<div id="controls-bottom">';
		echo $this->controls_items_per_page;
		echo $this->controls_pagination;
		echo '<div class="clear"></div>';
		echo '</div>';
	}

	/**
	* Outputs the filter controls
	*/
	public function outputFilters()
	{
		if (!$this->controls_filters) {
			return;
		}

		echo '<section id="controls-filters">';
		echo '<h3>' . el('controls_filter') . '</h3>';
		echo $this->controls_filters;
		echo '</section>';
	}

	/**
	* Outputs the order controls
	*/
	public function outputOrder()
	{
		if (!$this->controls_order) {
			return;
		}

		echo '<section id="controls-order">';
		echo '<h3>' . el('controls_order') . '</h3>';
		echo $this->controls_order;
		echo '</section>';
	}

	/**
	* Outputs the items per page control
	*/
	public function outputItemsPerPage()
	{
		echo $this->controls_items_per_page;
	}

	/**
	* Outputs the pagination
	*/
	public function outputPagination()
	{
		echo $this->controls_pagination;
	}
}
