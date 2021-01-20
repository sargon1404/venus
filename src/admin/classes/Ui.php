<?php
/**
* The User Interface Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The User Interface Class
* Builds UI related elements
*/
class Ui extends \Venus\Ui
{
	/**
	* Builds a tab
	* @public string $title The title of the tab
	* @public string $id The id of the tab
	* @return string The html code
	*/
	public function getTab(string $title, string $id) : string
	{
		return '<a href="javascript:venus.tab.switch(\'' . App::ejs($id) . '\')" id="tab-' . App::e($id) . '">' . App::e($title) . '</a>';
	}

	/**
	* Builds a details table, to be used in a details dialog
	* @param array $details The info in the format name => value
	* @return string The html code
	*/
	public function buildDetails(array $details) : string
	{
		$html = '<table class="info">';

		foreach ($details as $name => $val) {
			$html.= '<tr><td><strong>' . App::e($name) . '</strong></td><td>' . $val . '</td></tr>';
		}

		$html.= '</table>';

		return $html;
	}

	/**
	* Builds an editor
	* @param string $name The name of the field
	* @param string $content The content of the editor
	* @param string $webstorage_name The name under which the content of the editor will be periodically saved
	* @param string $webstorage_item_id Optional ID for the $webstorage_name
	* @param bool $read_more If true,will display the Read me button,from where a custom readme url/text can be entered
	* @param string $read_more_url The value for the read more url if $read_more is true
	* @param string $read_more_text The value for the read more text if $read_more is true
	* @param bool $show_page_break If true will show the Page Break button
	* @param bool $show_previous_versions If true will show the Previous Versions button
	* @param string $previous_versions_code Javascript code to be executed when the previous versions button is clicked
	* @param string $width The width of the editor
	* @param string $height	 The height of the editor. If set to 100% the editor will be automatically resized to cover the entire page\s height
	* @return string The html code of the editor
	*/
	public function buildEditor($name = '', $content = '', $webstorage_name = '', $webstorage_item_id = 0, $show_read_more = false, $read_more_url = '', $read_more_text = '', $show_toolbar = true, $show_page_break = false, $show_dynamic_snippets = false, $show_previous_versions = false, $previous_versions_code = '', $width = '100%', $height = '300px')
	{
		global $venus;
		if (!$name) {
			$name = 'venus_editor';
		}

		static $keep_alive = true;
		static $toolbar_loaded = false;

		$venus->plugins->run('admin_html_build_editor1', $name, $content, $webstorage_name, $webstorage_item_id, $show_read_more, $read_more_url, $read_more_text, $show_page_break, $show_dynamic_snippets, $show_previous_versions, $previous_versions_code, $width, $height);

		$html = '';
		$show_toggle = false;
		$load_toolbar = false;

		if ($show_toolbar) {
			if (!$toolbar_loaded) {
				$toolbar_loaded = true;
				$load_toolbar = true;
			}
		}

		if ($venus->user->editor == 'wysiwyg') {
			$html = $this->buildWysiwygEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
			$show_toggle = true;
		} elseif ($venus->user->editor == 'bbcode') {
			$html = $this->buildBbcodeEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
		} else {
			$html = $this->buildTextareaEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
		}

		$venus->dialogs->loadDialog('smilies', $name, ['field_name' => $name]);

		$venus->plugins->run('admin_html_build_editor2', $html_start, $html_middle1, $html_middle2, $html_end);

		$show_snippets = true;
		$show_templates = false;
		if (!$venus->cache->snippets_count) {
			$show_snippets = false;
			$show_dynamic_snippets = false;
		}

		if ($venus->user->editor == 'wysiwyg') {
			$theme = $venus->theme->getDefaultTheme();
			$templates_path = $venus->theme->getDir($theme->name) . VENUS_THEMES_EDITOR_TEMPLATES;

			if (is_dir($templates_path)) {
				$show_templates = true;
			}
		}

		$editor_data = [
			'html' => $html,
			'name' => $name,
			'width' => $width,
			'height' => $height,
			'show_read_more' => $show_read_more,
			'read_more_url' => $read_more_url,
			'read_more_text' => $read_more_text,
			'show_page_break' => $show_page_break,
			'show_snippets' => $show_snippets,
			'show_dynamic_snippets' => $show_dynamic_snippets,
			'show_previous_versions' => $show_previous_versions,
			'previous_versions_code' => $previous_versions_code,
			'show_templates' => $show_templates,
			'show_toggle' => $show_toggle,
			'load_toolbar' => $load_toolbar,
			'show_toolbar' => $show_toolbar,
			'id' => $webstorage_name
		];

		$editor = new \venus\Object($editor_data);
		$editor->addExtraData(['extra1', 'extra2' , 'extra3', 'extra4', 'extra_toolbar1', 'extra_toolbar2', 'extra_toolbar3']);

		$venus->plugins->run('admin_html_build_editor3', $editor);

		$venus->theme->addVar('editor', $editor);

		$keep_alive = false;

		return $venus->theme->getTemplate('admin_editor');
	}

	/**
	* Builds the wysiwyg editor
	*/
	public function buildWysiwygEditor($name = 'venus_editor', $content = '', $keep_alive = false, $webstorage_name = '', $webstorage_item_id = 0, $width = '100%', $height = '300px', $class = '', $id = '', $css_array = [])
	{
		return parent::buildWysiwygEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, $width, $height, $class, $id, $this->getWysiwygEditorCss());
	}

	/**
	* Returns the css files required by the wysiwyg editor
	*/
	public function getWysiwygEditorCss()
	{
		global $venus;
		$theme = $venus->theme->getDefaultTheme();

		//load the css of the default theme,ckeditor and the editors/wysiwyg.css file if any
		$css_array =
		[
			$venus->uri->build($venus->url_static . VENUS_ASSETS_NAME . 'css.php', ['theme' => $theme->name, 'dateline' => $venus->cache->css_dateline]), 	//the theme's css file
			VENUS_EDITORS_URL . 'ckeditor/contents.css', 	// ckeditor's content css
			$venus->theme->dir_url . VENUS_THEMES_CSS_DIR . 'editors/editor.css'	//admin theme's css file
		];

		$editor_wysiwyg_path = VENUS_THEMES_DIR . sl($theme->name) . VENUS_THEMES_CSS_DIR . 'editors/editor.css';

		if (is_file($editor_wysiwyg_path)) {
			$css_array[] = VENUS_THEMES_URL . sl(rawurlencode($theme->name)) . VENUS_THEMES_CSS_DIR . 'editors/editor.css';
		}

		return $css_array;
	}

	/**
	* Builds the area from where the permissions for the current item can be set
	* @param array $usergroups_data The usergroup data as returned by method TraitContentPermissions->_build_permissions
	* @param bool $show_all If true,will show the 'All' checkbox
	* @param bool $permissions_change_name Specifies the name of the Change Permissions radio. If empty,the radio is not displayed
	* @param bool $permissions_change If $permissions_change_name is not empty,controls which option is selected
	* @param string $title_width The width of the usergroup column.
	* @param string $perm_width The width of the permissions columns.
	* @param string $inherit_width The width of the inherit column.
	* @param string $all_width The width of the all column.
	* @return string The html code
	*/
	public function buildPermissions($usergroups_data, $permissions_change_name = '', $permissions_change = false, $show_all = true, $title_width = '30', $perm_width = '7', $inherit_width = '10', $all_width = '7')
	{
		$all_permissions_array = [
			'view' => l('permissions_view'),
			'comment' => l('permissions_comment'),
			'rate' => l('permissions_rate'),
			'add' => l('permissions_add'),
			'publish' => l('permissions_publish'),
			'publish_own' => l('permissions_publish_own'),
			'edit' => l('permissions_edit'),
			'edit_own' => l('permissions_edit_own'),
			'delete' => l('permissions_delete'),
			'delete_own' => l('permissions_delete_own'),
			'inherit' => l('permissions_inherit')
		];

		$title_width = (int)trim(str_replace('%', '', $title_width));
		$perm_width = (int)trim(str_replace('%', '', $perm_width));
		$inherit_width = (int)trim(str_replace('%', '', $inherit_width));
		$all_width = (int)trim(str_replace('%', '', $perm_width));

		//compute the width of empty gap/the number of columns based on the permissions declared for the first item
		//store in $permissions_display_array the permissions which are actually used
		$show_inherit = false;
		$js_permissions = [];
		$js_inherit = '';
		$cols = 1;
		$width = $title_width;
		$item = reset($usergroups_data);
		$permissions_array = [];

		if ($show_all) {
			$width+= $all_width;
			$cols++;
		}

		foreach ($all_permissions_array as $type => $text) {
			if (!isset($item[$type])) {
				continue;
			}

			if ($type != 'inherit') {
				$width+= $perm_width;
				$js_permissions[] = 'perm_' . $type;
				$permissions_array[$type] = $text;
			} else {
				$show_inherit = true;
				$width+= $inherit_width;
				$js_inherit = ",'perm_inherit'";
			}

			$cols++;
		}

		$gap_width = 100 - $width;
		$gap_td = '';
		if ($gap_width > 0) {
			$gap_td = '<td style="width:' . $gap_width . '%">&nbsp;</td>' . "\n";
			$cols++;
		}

		//output the required javascript
		$html = '<script type="text/javascript">//<![CDATA[
					var permissions = ' . $venus->javascript->toArray($js_permissions) . ';
					var perm = new venus_admin_permissions(permissions,' . $venus->javascript->toArray($usergroups_data, false, 'id') . $js_inherit . ');
					//]]></script>';

		//output the can change radios
		$change_code = '';
		$change_onclick = '';
		if ($permissions_change_name) {
			$html.= '<div class="right"><label for="' . e($permissions_change_name) . '0">' . el('permissions_change') . '</label>&nbsp;' . $venus->html->radioYesNo($permissions_change_name, $permissions_change) . '<hr /></div>';

			$permissions_change_name = ejs($permissions_change_name);
			$change_onclick = " onclick=\"venus.check('{$permissions_change_name}0')\" ";
			$change_code = "venus.check('{$permissions_change_name}0');";
		}

		$html.= '<table class="permissions">' . "\n";

		$html.= $this->buildPermissionsHeader($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_onclick, $gap_td, $title_width, $perm_width, $inherit_width, $all_width);

		$html.= $this->buildPermissionsBody($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_code, $change_onclick, $gap_td, $title_width, $perm_width, $inherit_width, $all_width);

		$html.= '<tr><td colspan="' . $cols . '" class="thr"><hr /></td></tr>' . "\n";

		$html.= $this->buildPermissionsFooter($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_code, $gap_td, $title_width, $perm_width, $inherit_width, $all_width);

		$html.= '</table>' . "\n";

		return $html;
	}

	protected function buildPermissionsBody($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_code, $change_onclick, $gap_td, $title_width, $perm_width, $inherit_width, $all_width)
	{
		$html = '';

		foreach ($usergroups_data as $item) {
			$permissions = [];
			$ugid = $item['ugid'];
			$title = '<label for="perm_view' . $ugid . '">' . e($item['usergroup_title']) . '</label>';

			if ($show_all) {
				$all_field = '<input type="checkbox" onclick="perm.toggle_all(this,' . $ugid . ');' . $change_code . '" name="perm_all[]" value="' . $ugid . '" id="perm_all' . $ugid . '" /><label for="perm_all' . $ugid . '" data-tooltip="' . e(el('permissions_all')) . '"></label>';
			}
			if ($show_inherit) {
				$inherit_field = '<input type="checkbox" onclick="perm.toggle_inherit_row(this,' . $ugid . ');' . $change_code . '" name="perm_inherit[]" value="' . $ugid . '" id="perm_inherit' . $ugid . '" ' . $item['inherit'] . ' /><label for="perm_inherit' . $ugid . '" data-tooltip="' . e(el('permissions_inherit')) . '"></label>';
			}

			foreach ($permissions_array as $type => $text) {
				$id = $type . $ugid;
				$permissions[] = '<input type="checkbox" ' . $item['disabled'] . $change_onclick . ' name="perm_' . $type . '[]" value="' . $ugid . '" id="perm_' . $id . '" ' . $item[$type] . ' /><label for="perm_' . $id . '" data-tooltip="' . e(e($text)) . '"></label>';
			}

			$html.= $this->buildPermissionsRow($permissions, $title, $gap_td, $all_field, $inherit_field, $title_width, $perm_width, $inherit_width, $all_width);
		}

		return $html;
	}

	protected function buildPermissionsHeader($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_onclick, $gap_td, $title_width, $perm_width, $inherit_width, $all_width)
	{
		$all_field = '';
		$inherit_field = '';
		$permissions = [];

		if ($show_all) {
			$all_field = '<a href="javascript:venus.get(\'perm_all0\').click()"' . $change_onclick . '>' . el('permissions_all') . '</a>';
		}

		if ($show_inherit) {
			$inherit_field = '<a href="javascript:venus.get(\'perm_inherit0\').click()" ' . $change_onclick . '>' . el('permissions_inherit') . '</a>';
		}

		foreach ($permissions_array as $type => $text) {
			$permissions[] = '<a href="javascript:venus.get(\'' . $type . '0\').click()"' . $change_onclick . '>' . e($text) . '</a>';
		}

		return $this->buildPermissionsRow($permissions, '&nbsp;', $gap_td, $all_field, $inherit_field, $title_width, $perm_width, $inherit_width, $all_width);
	}

	protected function buildPermissionsFooter($usergroups_data, $permissions_array, $show_all, $show_inherit, $change_code, $gap_td, $title_width, $perm_width, $inherit_width, $all_width)
	{
		global $venus;
		$all_field = '';
		$inherit_field = '';
		$permissions = [];

		if ($show_all) {
			$all_field = '<input type="checkbox" name="perm_all0" id="perm_all0" onclick="perm.toggle_all_rows(\'perm_all\'); venus.html.toggle_checkboxes(\'perm_inherit\',false);' . $change_code . '" /><label for="perm_all0" data-tooltip="' . e(el('permissions_all')) . '"></label>';
		}

		if ($show_inherit) {
			$all_checked = $this->getPermissionsAllState('inherit', $usergroups_data);

			$inherit_field = '<input type="checkbox" name="perm_inherit0" id="perm_inherit0" ' . $venus->html->checked($all_checked) . ' onclick="perm.toggle_inherit(this.checked);' . $change_code . '" /><label for="perm_inherit0" data-tooltip="' . e(el('permissions_inherit')) . '"></label>';
		}

		foreach ($permissions_array as $type => $text) {
			$all_checked = $this->getPermissionsAllState($type, $usergroups_data);

			$permissions[] = '<input type="checkbox" name="' . $type . '0" id="' . $type . '0" ' . $venus->html->checked($all_checked) . ' onclick="perm.toggle_checkboxes(\'perm_' . $type . '\',this.checked);' . $change_code . '" /><label for="' . $type . '0" data-tooltip="' . e(e($text)) . '"></label>';
		}

		return $this->buildPermissionsRow($permissions, '&nbsp;', $gap_td, $all_field, $inherit_field, $title_width, $perm_width, $inherit_width, $all_width);
	}

	/**
	* Builds a permissions row
	*/
	protected function buildPermissionsRow($permissions, $title, $gap_td, $all_field, $inherit_field, $title_width, $perm_width, $inherit_width, $all_width)
	{
		$html = '<tr>' . "\n";
		$html.= '<td style="width:' . $title_width . '%">' . $title . '</td>' . "\n";
		foreach ($permissions as $text) {
			$html.= '<td style="width:' . $perm_width . '%">' . $text . '</td>' . "\n";
		}

		//display the columns gap
		$html.= $gap_td;

		if ($all_field) {
			$html.= '<td style="width:' . $all_width . '%">' . $all_field . '</td>' . "\n";
		}
		if ($inherit_field) {
			$html.= '<td style="width:' . $inherit_width . '%">' . $inherit_field . '</td>' . "\n";
		}

		$html.= '</tr>' . "\n";

		return $html;
	}

	protected function getPermissionsAllState($type, $usergroups_data)
	{
		$checked = 0;
		$uncheked = 0;
		foreach ($usergroups_data as $item) {
			if ($item[$type]) {
				$checked++;
			} else {
				$uncheked++;
			}
		}

		if ($checked >= $uncheked) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Builds the categories tree
	* @param array $check_ids Array with the categories to check. If null,only the default 'Uncategoried' category is checked. If $check_all_categories is true, *all* the categories are checked and this param is ignored
	* @param bool $check_all_categories If true,the 'all categories' checkbox will be selected, along with all the checkboxes
	* @param bool $show_uncategoriezed If true, will add an 'Uncategorized' option with id = 0
	* @param bool $change_categories_name The name of the Change Categories radio. If empty,it will not be displayed
	* @param bool $change_categories If $change_categories_name is specified,controls which option is selected*
	* @param bool show_all_categories If true, will show the 'All Categories' checkbox
	* @param string $categories_name The name applied to the categories checkboxes
	* @param string $all_categories_name The name of the 'all_categories' checkbox
	* @return string The html code
	*/
	public function buildCategoriesTree($check_ids = null, $check_all_categories = false, $show_uncategoriezed = false, $change_categories_name = '', $change_categories = false, $show_all_categories = true, $categories_name = 'categories', $all_categories_name = 'all_categories')
	{
		global $venus;
		$check_ids = to_array($check_ids);
		if ($check_ids === null) {
			$check_ids = [0];
		} elseif (!is_array($check_ids)) {
			$check_ids = [VENUS_CATEGORY_HOMEPAGE];
		}

		//get the defined category from the db; add the show_uncategoriezed option, if required
		$categories_array = $venus->db->selectArray('venus_categories', 'cid, title, parent, level', 'ORDER BY position');
		$categories_array = to_array($categories_array);
		if ($show_uncategoriezed) {
			array_unshift($categories_array, ['cid' => -1, 'title' => l('uncategorized'), 'parent' => 0, 'level' => 0]);
		}

		//read the check ids from post, if defined
		$is_post = $venus->request->isPost('venus_categories_tree');
		if ($is_post) {
			//read from post the check_ids and check_all
			$check_all_categories = $venus->request->post($all_categories_name);
			$check_ids = $venus->request->postArray($categories_name, 'i');
		}

		//output the categories tree
		$html = $venus->html->requestHidden('venus_categories_tree', '1');

		//output the required javascript
		$html.= '<script type="text/javascript">//<![CDATA[
					var categories_tree = new venus_admin_categories_tree(\'' . ejs($all_categories_name) . '\', \'' . ejs($change_categories_name) . '\');
				  //]]></script>';

		//output the can change radios
		if ($change_categories_name) {
			$html.= '<div class="categories-tree-change">
						<label for="' . e($change_categories_name) . '0">' . el('categories_change') . '</label>
						&nbsp;' . $venus->html->radioYesNo($change_categories_name, $change_categories) .
					 '</div>';
		}

		$html.= '<div class="categories-tree">';

		if ($show_all_categories) {
			$html.= $venus->html->checkbox($all_categories_name, l('all_categories'), 1, $check_all_categories, 'categories_tree_all', $all_categories_name, "onclick=\"categories_tree.toggle_all(this.checked)\"");
			$html.= '<hr />';
		}

		$html.= '<ul>';
		$html.= $this->buildCategoryTree($categories_array, 0, $check_ids, $check_all_categories, $categories_name);
		$html.= '</ul>';

		$html.= '</div>';

		return $html;
	}

	/**
	* Builds a category leaf
	* @return string The html code
	*/
	protected function buildCategoryTree($categories_array, $parent, $check_ids, $check_all_categories, $categories_name)
	{
		global $venus;
		if (!$categories_array) {
			return '';
		}

		$i = 0;
		$html = '';

		foreach ($categories_array as $index => $category) {
			if ($category['parent'] != $parent) {
				continue;
			}

			$cid_value = $category['cid'];

			//set the uncategorized value to 0
			if ($cid_value == -1) {
				$cid_value = 0;
			}

			if (!$i && $parent) {
				$html.= '<ul>';
			}

			$checked = false;
			if ($check_all_categories) {
				$checked = true;
			} else {
				$checked = in_array($cid_value, $check_ids);
			}

			$check_array = '[]';
			$subcategories = $this->getCategoryChildren($categories_array, $index, $category['level']);
			if ($subcategories) {
				$check_array = '[' . implode(',', $subcategories) . ']';
			}

			$html.= '<li>';
			$html.= $venus->html->checkbox($categories_name . '[]', $category['title'], $cid_value, $checked, 'categories_tree_root', $categories_name . $cid_value, "onclick=\"categories_tree.select_item({$check_array},this)\"");

			$html.= $this->buildCategoryTree($categories_array, $category['cid'], $check_ids, $check_all_categories, $categories_name);
			$html.= '</li>';
			$i++;
		}

		if ($i && $parent) {
			$html.= '</ul>';
		}

		return $html;
	}

	/**
	* Returns the child subcategories (including sub-sub categories)
	* @param array $categories_array Categories array sorted by category_position
	* @param int	The index in the array of the item for which we want the subcategories
	* @param int	The category_level of the item
	* @return array The children
	*/
	protected function getCategoryChildren(&$categories_array, $start, $start_level)
	{
		$subcategories = [];
		foreach ($categories_array as $i => $cat) {
			if ($i <= $start) {
				continue;
			}

			if ($cat['level'] <= $start_level) {
				break;
			}

			$subcategories[] = $cat['cid'];
		}
		return $subcategories;
	}
}
