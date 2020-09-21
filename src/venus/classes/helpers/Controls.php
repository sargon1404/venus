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
	* @var string $filter_ignore_value If the filter value equals $filter_ignore_value, that filter will not be included in the sql. Used to indicate select top options/hints
	*/
	public string $filter_ignore_value = '-1';

	/**
	* @var array $order_array Array storing the orders list
	*/
	protected array $order_array = [];

	/**
	* @var string $order_default_value The default order value to be returned, if no order control is selected
	*/
	protected string $order_default_value = '';

	/**
	* @var int $items_per_page The default number of items per page
	*/
	public int $items_per_page = 0;

	/**
	* Builds the controls object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		$this->items_per_page = $this->app->config->items_per_page;

		$this->session_key = $this->app->session->getPrefix() . $this->session_key;

		$this->sessionInit();

		$this->app->lang->loadFile('controls');
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
	* 'type' => The filter's type [input | select | input-hidden etc...]. If not specified, 'input' is used
	* 'field' => The database column corresponding to this filter. If not specified, the name/key of the filter is used
	* 'value' => The filter's default value
	* 'operator' => The filter type [=, !=, >, <, >=, <= ,like]. If not specified, 'like' is used
	* 'attributes' => [] The attributes of the control. Eg: 'attributes' => ['placeholder' => <placeholder>]
	* 'properties' => [] The properties of the control. Eg: 'properties' => ['options' => []]
	* 'filter' => Filtering to be applied on the value. Eg: i|f (int, float)
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
	* Outputs the filter controls
	*/
	public function outputFilters()
	{
		echo '<section id="controls-filters">' . "\n";
		echo '<h3>' . App::estr('controls_filter') . '</h3>' . "\n";
		echo $this->getFilters();
		echo $this->getFiltersButtons();
		echo '</section>' . "\n";
	}

	/**
	* Returns the html code for the filters
	* @return string The filter's html code
	*/
	public function getFilters() : string
	{
		if (!$this->filter_array) {
			return '';
		}

		$html = '<div class="controls-filter-fields">' . "\n";
		foreach ($this->filter_array as $name => $filter)
		{
			$name = "control-filter[{$name}]";
			$type = $filter['type'] ?? 'input';
			$attributes = $filter['attributes'] ?? [];
			$properties = $filter['properties'] ?? [];

			$attributes ['name'] = $name;

			$html.= '<div class="controls-filter-field">' . "\n";
			$html.= $this->app->html->getTag($type, $attributes, $properties)->get();
			$html.= '</div>' . "\n";
		}
		$html.= '</div>' . "\n";

		return $html;
	}

	/**
	* Returns the filter buttons: Filter & Reset
	* @return string The html code
	*/
	public function getFiltersButtons() : string
	{
		$html = '<div class="controls-filter-buttons">' . "\n";
		$html.= '<div id="controls-filters-action">';
		$html.= '<input type="submit" name="controls-filter-button" id="controls-filter-button" value="' . App::e($this->filter_button) . '"' . $onclick_filter . ' />';
		$html.= '</div>' . "\n";
		$html.= '<div class="loading-small" id="controls-filter-loading"></div>' . "\n";
		$html.= '<div class="clear"></div>' . "\n";

		$html.= '</div>' . "\n";

		return $html;
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












	/********************FILTER METHODS*******************************************/

	/**
	* Builds the filter controls
	*/
	protected function buildFilters() : string
	{
		if (!$this->filter_array) {
			return '';
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


		$html.= '</div>' . "\n";

		return $html;
	}


}
