<?php
/**
* The HTML Class
* @package Venus
*/

namespace Venus\Admin;

use Venus\Helpers\Tree;

/**
* The HTML Class
* Html generating methods
*/
class Html extends \Venus\Html
{
	/**
	* @var string $root String used as the root/first entry in select controls, if required
	*/
	public string $root = '-------------------';

	/**
	* Returns a select control with options : yes/no
	* @param string $name The name of the field
	* @param string $selected 1 for yes, 0 for no
	* @return string The html code
	*/
	public function selectYesNo(string $name, string $selected = '1') : string
	{
		$options = [
			'1' => App::__('yes'),
			'0' => App::__('no'),
		];

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_yes_no', $html, $name, $options, $selected, $this);
	}

	/**
	* Returns a select control with options : global/yes/no
	* @param string $name The name of the field
	* @param string $selected -1 for global, 1 for yes, 0 for no
	* @return string The html code
	*/
	public function selectGlobalYesNo(string $name, string $selected = '-1') : string
	{
		$options = [
			'-1' => App::__('global_setting'),
			'1' => App::__('yes'),
			'0' => App::__('no'),
		];

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_global_yes_no', $html, $name, $options, $selected, $this);
	}

	/**
	* Returns a select control with options : No Change/yes/no
	* @param string $name The name of the field
	* @param string $selected . for no change, 1 for yes, 0 for no
	* @return string The html code
	*/
	public function selectNochangeYesNo(string $name, string $selected = '.') : string
	{
		$options = [
			'.' => App::__('no_change'),
			'1' => App::__('yes'),
			'0' => App::__('no'),
		];

		$html = $this->select($name, $options, $selected);

		return $html = $this->app->plugins->filter('admin_html_select_nochange_yes_no', $html, $name, $options, $selected, $this);
	}

	/**
	* Returns three radio controls. global/yes/no
	* @param string $name The name of the field
	* @param string $selected -1 for global, 1 for yes, 0 for no
	* @return string The html code
	*/
	public function radioGlobalYesNo(string $name, string $selected = '-1') : string
	{
		$html = $this->radio($name, App::__('global'), ($selected == '-1' ? true : false), '-1');
		$html.= $this->radio($name, App::__('yes'), ($selected == '1' ? true : false), '1');
		$html.= $this->radio($name, App::__('no'), ($selected == '0' ? true : false), '0');

		return $this->app->plugins->filter('admin_html_radio_global_yes_no', $html, $name, $selected, $this);
	}

	/**
	* Returns three form radio controls. No Change/yes/no
	* @param string $name The name of the field
	* @param string $selected '.' for global, 1 for yes, 0 for no
	* @return string The html code
	*/
	public function radioNochangeYesNo(string $name, string $selected = '.') : string
	{
		$html = $this->radio($name, App::__('no_change'), ($selected == '.' ? true : false), '.');
		$html.= $this->radio($name, App::__('yes'), ($selected == '1' ? true : false), '1');
		$html.= $this->radio($name, App::__('no'), ($selected == '0' ? true : false), '0');

		return $this->app->plugins->filter('admin_html_radio_nochange_yes_no', $html, $name, $selected, $this);
	}

	/**
	* Returns four form radio controls. No change/Global/yes/no
	* @param string $name The name of the field
	* @param string $selected '.' for no change, -1 for global, 1 for yes, 0 for no
	* @return string The html code
	*/
	public function radioNochangeGlobalYesNo(string $name, string $selected = '.') : string
	{
		$html = $this->radio($name, App::__('no_change'), ($selected == '.' ? true : false), '.');
		$html.= $this->radio($name, App::__('global'), ($selected == '-1' ? true : false), '-1');
		$html.= $this->radio($name, App::__('yes'), ($selected == '1' ? true : false), '1');
		$html.= $this->radio($name, App::__('no'), ($selected == '0' ? true : false), '0');

		return $this->app->plugins->filter('admin_html_radio_nochange_global_yes_no', $html, $name, $selected, $this);
	}

	/**
	* Returns a select control from where the seo rel attribute can be selected
	* @param string $name The name of the field
	* @param string $selected The selected value
	* @param bool $show_no_change If true, will also display the 'No Change' option
	* @param bool $show_global If true, will also display the 'Global' option
	* @return string The html code
	*/
	public function selectSeoRel(string $name, string $selected = '', bool $show_no_change = false, bool $show_global = true) : string
	{
		$options = [
			'' => App::__('seo_rel1'),
			'nofollow' => App::__('seo_rel2')
		];

		if ($show_global) {
			$options = ['-1' => App::__('global_setting')] + $options;
		}
		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_seo_rel', $html, $name, $selected, $show_no_change, $show_global, $this);
	}

	/**
	* Returns a select control from where the seo target attribute can be selected
	* @param string $name The name of the field
	* @param string $selected The selected value
	* @param bool $show_no_change If true, will also display the 'No Change' option
	* @param bool $show_global If true, will also display the 'Global' option
	* @return string The html code
	*/
	public function selectSeoTarget(string $name, string $selected = '', bool $show_no_change = false, bool $show_global = true) : string
	{
		$options = [
			'' => App::__('seo_target1'),
			'_blank' => App::__('seo_target2')
		];

		if ($show_global) {
			$options = ['-1' => App::__('global_setting')] + $options;
		}
		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_seo_target', $html, $name, $selected, $show_no_change, $show_global, $this);
	}

	/**
	* Returns a select control from where the meta robots value can be selected
	* @param string $name The name of the field
	* @param string $selected The selected value
	* @param bool $show_no_change If true, will also display the 'No Change' option
	* @param bool $show_global If true, will also display the 'Global' option
	* @return string The html code
	*/
	public function selectMetaRobots(string $name, string $selected = '', bool $show_no_change = false, bool $show_global = true) : string
	{
		$options = [
			'' => '',
			'index, follow' => App::__('meta_robots1'),
			'index, nofollow' => App::__('meta_robots2'),
			'noindex, follow' => App::__('meta_robots3'),
			'noindex, nofollow' => App::__('meta_robots4')
		];

		if ($show_global) {
			$options = ['-1' => App::__('global_setting')] + $options;
		}
		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_meta_robots', $html, $name, $selected, $show_no_change, $show_global, $this);
	}

	/**
	* Returns a select control from where frequency of a sitemap page can be selected
	* @param string $name The name of the field
	* @param string $selected The selected value
	* @param bool $show_no_change If true, will also display the 'No Change' option
	* @param bool $show_global If true, will also display the 'Global' option
	* @return string The html code
	*/
	public function selectSitemapFrequency(string $name, string $selected = '1', bool $show_no_change = false, bool $show_global = true) : string
	{
		$options = [
			'1' => App::__('sitemap_frequency1'),
			'2' => App::__('sitemap_frequency2'),
			'3' => App::__('sitemap_frequency3'),
			'4' => App::__('sitemap_frequency4'),
			'5' => App::__('sitemap_frequency5'),
			'6' => App::__('sitemap_frequency6'),
			'7' => App::__('sitemap_frequency7')
		];

		if ($show_global) {
			$options = ['-1' => App::__('global_setting')] + $options;
		}
		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_sitemap_frequency', $html, $name, $selected, $show_no_change, $show_global, $this);
	}

	/**
	* Returns a select control from where priority of a sitemap page can be selected
	* @param string $name The name of the field
	* @param string $selected The selected value
	* @param bool $show_no_change If true,will also display the 'No Change' option
	* @param bool $show_global If true, will also display the 'Global' option
	* @return string The html code
	*/
	public function selectSitemapPriority(string $name, string $selected = '1', bool $show_no_change = false, bool $show_global = true) : string
	{
		$options = ['0' => 0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3, '0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6, '0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1];

		if ($show_global) {
			$options = ['-1' => App::__('global_setting')] + $options;
		}
		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$html = $this->select($name, $options, $selected);

		return $this->app->plugins->filter('admin_html_select_sitemap_priority', $html, $name, $selected, $show_no_change, $show_global, $class, $this);
	}

	/**
	* Outputs the select options needed to select a language
	* @param int $selected The selected language id
	* @return string The html code
	*/
	public function selectLanguageOptions(int $selected = 0) : string
	{
		$languages = $this->app->db->selectList('venus_languages', 'lid', 'title', ['status' => 1]);

		$options = [0 => l('language_all')] + $languages;

		return $this->selectOptions($options, $selected);
	}

	/**
	* Outputs the select options needed to select an image process type
	* @param string $selected The selected value
	* @return string The html code
	*/
	public function selectImageProcessOptions(string $selected = 'resize') : string
	{
		$options = [
			'resize' => App::__('image_resize'),
			'cut_resize' => App::__('image_cut_resize'),
			'cut' => App::__('image_cut')
		];

		return $this->selectOptions($options, $selected);
	}

	/**
	* Returns the options of a tree (Eg: categories/menu tree)
	* @param array $items The items
	* @param array $exclude_ids Array with the ids to exclude from the tree
	* @param bool $show_no_change If true, will also display the 'No Change' option
	* @param bool $show_root If true, will show the 'root' of the control
	* @return array The tree options
	*/
	protected function getTreeOptions(array $items, array $exclude_ids = [], bool $show_no_change = false, bool $show_root = true) : array
	{
		$tree = new Tree;

		if ($exclude_ids) {
			$parent_ids = [];
			foreach ($items as $id => $item) {
				$parent_ids[$id] = $item['parent'];
			}

			$exclude_items = $tree->getItemAndSubitems($exclude_ids, $parent_ids);

			if ($exclude_items) {
				foreach ($exclude_items as $id) {
					unset($items[$id]);
				}
			}
		}

		$options = $tree->create($items, 'title', 'level');

		if ($show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}
		if ($show_root) {
			$options = ['0' => $this->root] + $options;
		}

		return $options;
	}

	/**
	* Returns a select control from where a category can be selected
	* @param string $name The name of the field
	* @param int $selected_category The category ID of the selected category
	* @param array $exclude_cids Array with the category ids which aren't shown in the dropdown
	* @param bool $required If true, the select control will be required
	* @param bool $show_no_change If true,will also display the 'No Change' option
	* @param bool $show_root If true,will show the 'root' of the control
	* @param bool $return_only_options If true, will return only the code for the options, not the entire <select>
	* @return string The html code
	*/
	public function selectCategory(string $name, int $selected_category = 0, array $exclude_cids = [], bool $required = false, bool $show_no_change = false, bool $show_root = true, bool $return_only_options = false) : string
	{
		$categories = $this->app->db->selectArrayWithKey('venus_categories', 'cid', 'cid, title, parent, level', [], 'position');

		$options = $this->getTreeOptions($categories, $exclude_cids, $show_no_change, $show_root);

		if ($return_only_options) {
			return $this->selectOptions($options, $selected_category);
		} else {
			return $this->select($name, $options, $selected_category, $required);
		}
	}

	/**
	* Returns a the options for a select control from where a category can be selected
	* @param int $selected_category The category ID of the selected category
	* @param array $exclude_cids Array with the category ids which aren't shown in the dropdown
	* @param bool $show_no_change If true,will also display the 'No Change' option
	* @param bool $show_root If true,will show the 'root' of the control
	* @return string The html code
	*/
	public function selectCategoryOptions(int $selected_category = 0, array $exclude_cids = [], bool $show_no_change = false, bool $show_root = true) : string
	{
		return $this->selectCategory('', $selected_category, $exclude_cids, false, $show_no_change, $show_root, true);
	}

	/**
	* Returns a select control from where a menu can be selected
	* @param int $menu_id The menu's id
	* @param string $name The name of the field
	* @param int $selected_menu The menu ID of the selected menu
	* @param array $exclude_mids Array with the menu ids which aren't shown in the dropdown
	* @param bool $required If true, the select control will be required
	* @param bool $show_no_change If true,will also display the 'No Change' option
	* @param bool $show_root If true,will show the 'root' of the control
	* @param bool $return_only_options If true, will return only the code for the options, not the entire <select>
	* @return string The html code
	*/
	public function selectMenu(int $menu_id, string $name, int $selected_menu = 0, array $exclude_mids = [], bool $required = false, bool $show_no_change = false, bool $show_root = true, bool $return_only_options = false) : string
	{
		$menus = $this->app->db->selectArrayWithKey('venus_menu_entries', 'mid', 'mid, title, parent, level', ['menu' => $menu_id], 'position');

		$options = $this->getTreeOptions($menus, $exclude_mids, $show_no_change, $show_root);

		if ($return_only_options) {
			return $this->selectOptions($options, $selected_menu);
		} else {
			return $this->select($name, $options, $selected_menu, $required);
		}
	}

	/**
	* Returns a the options for a select control from where a menu can be selected
	* @param int $menu_id The menu's id
	* @param int $selected_menu The menu ID of the selected menu
	* @param array $exclude_mids Array with the menu ids which aren't shown in the dropdown
	* @param bool $show_no_change If true,will also display the 'No Change' option
	* @param bool $show_root If true,will show the 'root' of the control
	* @return string The html code
	*/
	public function selectMenuOptions(int $menu_id, int $selected_menu = 0, array $exclude_mids = [], bool $show_no_change = false, $show_root = true) : string
	{
		return $this->selectMenu($menu_id, '', $selected_menu, $exclude_mids, false, $show_no_change, $show_root, true);
	}

	/**
	* Builds an user select area
	* @param string $name The name of the control. If empty user_select will be used
	* @param string $selected The username of the user that should be selected by default
	* @param bool $required True if the field is required
	* @param string $placeholder Placeholder text, if any
	* @param bool $onkeyup If true, will show the user options on keyup
	* @param string $class The class of the control.
	* @param string $uid_name The name of the hidden field which will be populated with the uid. If empty {$name}_uid is used
	* @return string The html code
	*/
	public function selectUser(string $name = 'user_select', string $selected = '', bool $required = false, string $placeholder = '', bool $onkeyup = true, string $class = 'select-user', string $uid_name = '') : string
	{
		if (!$name) {
			$name = 'user_select';
		}
		if (!$uid_name) {
			$uid_name = $name . '_uid';
		}
		if (!$placeholder) {
			$placeholder = App::__('user_select_text1');
		}
		if ($onkeyup) {
			$onkeyup = 'venus.populate.show_users(event, this)';
		}

		$id = $name;
		$onclick = 'venus.populate.show(event,\'' . App::ejs($id, false) . '\', \'get_users\', \'\', true)';

		$uid = 0;
		$username = '';
		if ($selected) {
			$username = $selected;
			$uid = $this->app->user->getUidByUsername($username);
		}

		$html = $this->requestHidden($uid_name, $uid, $uid_name);
		$html.= $this->requestText($name, $username, $required, $placeholder, $class, $id, ['autocomplete' => 'off', 'onkeyup' => [$onkeyup]]);
		$html.= '&nbsp;';
		$html.= $this->requestButton('', App::__('user_select_text2'), '', '', ['onclick' => [$onclick]]);

		return $this->app->plugins->filter('admin_html_select_user', $html, $name, $selected, $required, $placeholder, $onkeyup, $onclick, $class, $id, $this);
	}

	/**
	* Builds a page select area
	* @param string $name The name of the control. If empty page_select will be used
	* @param mixed $selected The id of the page that should be selected by default, if the param is an int/string; If the param is an array, the title will be read from the first element of the array
	* @param bool $required True if the field is required
	* @param string $placeholder Placeholder text
	* @param bool $onkeyup If true, will show the page options on keyup
	* @param string $class The class of the control.
	* @param string $pid_name The name of the hidden field which will be populated with the pid. If empty {$name}_pid is used
	* @return string The html code
	*/
	public function selectPage(string $name = 'page_select', $selected = 0, bool $required = false, string $placeholder = '', bool $onkeyup = true, string $class = 'select-page', string $pid_name = '') : string
	{
		if (!$name) {
			$name = 'page_select';
		}
		if (!$pid_name) {
			$pid_name = $name . '_pid';
		}
		if (!$placeholder) {
			$placeholder = App::__('page_select_text1');
		}
		if ($onkeyup) {
			$onkeyup = 'venus.populate.show_pages(event, this)';
		}

		$id = $name;

		$pid = 0;
		$page_title = '';

		if ($selected) {
			if (is_array($selected)) {
				$page_title = reset($selected);
			} else {
				$pid = $selected;
				$page_title = (string)$this->app->db->selectResult('venus_pages', 'title', ['pid' => (int)$pid]);
			}
		}

		$html = $this->requestHidden($pid_name, $pid, $pid_name);
		$html.= $this->requestText($name, $page_title, $required, $placeholder, $class, $id, ['autocomplete' => 'off', 'onkeyup' => [$onkeyup]]);
		$html.= '&nbsp;';
		$html.= $this->requestButton('', App::__('page_select_text2'));

		$html = $this->app->plugins->filter('admin_html_select_page', $html, $name, $selected, $required, $placeholder, $onkeyup, $class, $id, $this);

		return $html;
	}

	/**
	* Builds a select control from where the order of an item can be chosen
	* @param string $name The name of the select control
	* @param int $current_item_id The id for the current item, if any
	* @param string $selected_value The value of the selected item
	* @param array $items Array with all the defined items
	* @param string $item_title_field The name of the field containing the item's title
	* @param string $item_order_field The name of the field containing the item's order value
	* @param bool $reverse_order If true, the items are in reverse order
	* @param bool $return_only_options If true will return only the options
	* @return string The html code
	*/
	public function selectOrder(string $name, int $current_item_id, string $selected_value, array $items, string $item_title_field, string $item_order_field, bool $reverse_order = false, bool $return_only_options = false) : string
	{
		$first_index = 0;
		$last_index = -1;
		if ($reverse_order) {
			$first_index = -1;
			$last_index = 0;
		}

		$order_array = [];
		$items_count = count($items);

		if (!$items_count) {
			//no items; show only yhe 'first' option
			$order_array[$first_index] = App::__('order_first');
		} elseif ($items_count == 1) {
			if ($current_item_id) {
				foreach ($items as $id => $item) {
					$order_array[$item[$item_order_field]] = App::__('order_first');
				}
			} else {
				$order_array[$first_index] = App::__('order_first');
				$order_array[$last_index] = App::__('order_last');
			}
		} else {
			//get the id of the item next in the list compared with the current item
			$previous_item_id = 0;
			$current_item_order = 0;
			$is_first = false;
			$is_last = false;

			if ($current_item_id) {
				$i = 0;
				foreach ($items as $id => $item) {
					if ($id == $current_item_id) {
						$current_item_order = $item[$item_order_field];
						if (!$i) {
							$is_first = true;
							$order_array[$item[$item_order_field]] = App::__('order_first');
						}

						break;
					}

					$previous_item_id = $id;
					$i++;
				}
			}

			if (!$is_first) {
				$order_array[$first_index] = App::__('order_first');
			}

			$i = 0;
			foreach ($items as $id => $item) {
				if ($i == $items_count -1) {
					if ($id == $current_item_id) {
						$is_last = true;
						$order_array[$item[$item_order_field]] = App::__('order_last');

						continue;
					} else {
						continue;
					}
				}

				$i++;

				if ($id == $current_item_id) {
					continue;
				}

				if ($current_item_id && $id == $previous_item_id) {
					$order_array[$current_item_order] = App::__('order_after', '{NAME}', $item[$item_title_field], false);
					continue;
				}

				$order_array[$item[$item_order_field]] = App::__('order_after', '{NAME}', $item[$item_title_field], false);
			}

			if (!$is_last) {
				$order_array[$last_index] = App::__('order_last');
			}
		}

		if ($return_only_options) {
			return $this->selectOptions($order_array, $selected_value);
		} else {
			return $this->select($name, $order_array, $selected_value);
		}
	}

	/**
	* Builds the options for a select control from where the order of an item can be chosen
	* @param int $current_item_id The id for the current item, if any
	* @param string $selected_value The value of the selected item
	* @param array $items Array with all the defined items
	* @param string $item_title_field The name of the field containing the item's title
	* @param string $item_order_field The name of the field containing the item's order value
	* @param bool $reverse_order If true, the items are in reverse order
	* @return string The html code
	*/
	public function selectOrderOptions(int $current_item_id, string $selected_value, array $items, string $item_title_field, string $item_order_field, bool $reverse_order = false) : string
	{
		return $this->selectOrder('', $current_item_id, $selected_value, $items, $item_title_field, $item_order_field, $reverse_order, true);
	}
}
