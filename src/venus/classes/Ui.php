<?php
/**
* The User Interface Class
* @package Venus
*/

namespace Venus;

/**
* The User Interface Class
*/
class Ui extends \Mars\Ui
{
	/**
	* Builds the pagination template. The number of pages is computed as $total_items/$items_per_page.
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items that should be displayed on each page. $this->app->config->items_per_page will be used if 0
	* @param string $page_param The name of the 'get' param into which the number of the page will be stored
	* @param string $is_seo_url If true will try to replace $seo_page_param from $base_url with the page number rather than append the page number as a param
	* @param string $seo_page_param The string found in $base_url which will be replaced by the page number if $is_seo_url is true
	* @param bool $use_ajax If true, will load the pages using ajax
	* @return string The html
	*/
	public function buildPagination(string $base_url, int $total_items, int $items_per_page = 0, string $page_param = '', bool $is_seo_url = false, string $seo_page_param = '{PAGE_NO}', int $max_links = 10, bool $use_ajax = false, string $update_element_id = '', string $js_function = '', string $js_function_params = '') : string
	{
		$max_links = $this->app->config->pagination_max_links;
		if (!$items_per_page) {
			$items_per_page = $this->app->config->items_per_page;
		}
		if (!$page_param) {
			$page_param = $this->app->config->page_param;
		}
		if (!$seo_page_param) {
			$seo_page_param = '{PAGE_NO}';
		}

		$pag = $this->getPaginationObj();
		$pag->page_param = $page_param;
		$pag->seo_page_param = $seo_page_param;
		$pag->max_links = $max_links;

		$current_page = $this->app->request->getPage($page_param);

		return $pag->get($base_url, $current_page, $total_items, $items_per_page, $is_seo_url, $use_ajax, $update_element_id, $js_function, $js_function_params, $is_dialog);
	}

	protected function getPaginationObj()
	{
		return new Ui\Pagination;
	}

	/**
	* Builds the pagination template with ajax links. The number of pages is computed as $total_items/$items_per_page.
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items that should be displayed on each page.$this->app->config->items_per_page will be used if 0
	* @param string $update_element_id The id of the DOM element which will be updated with the content of the next page
	* @param string $page_param The name of the 'get' param into which the number of the page will be stored
	* @param string $js_function the javascript function called when changing the page. If empty no function is used and the content of element content are automatically updated
	* @param string $js_function_params Params to pass to the javascript function
	* @param string $is_seo_url If true will try to replace $seo_page_param from $base_url with the page number rather than append the page number as a param
	* @param string $seo_page_param The string found in $base_url which will be replaced by the page number if $is_seo_url is true
	* @return string The html code of the pagination
	*/
	public function buildAjaxPagination(string $base_url, int $total_items, int $items_per_page = 0, string $update_element_id = '', string $page_param = '', string $js_function = '', string $js_function_params = '', bool $is_seo_url = false, string $seo_page_param = '{PAGE_NO}', int $max_links = 10) : string
	{
		return $this->buildPagination($base_url, $total_items, $items_per_page, $page_param, $is_seo_url, $seo_page_param, $max_links, true, $update_element_id, $js_function, $js_function_params);
	}

	/**
	* Builds an editor
	* @param string $name The name of the field
	* @param string $content The content of the editor
	* @param string $webstorage_name The name under which the content of the editor will be periodically saved
	* @param string $webstorage_item_id Optional ID for the $webstorage_name. If -1, the text of the editor won't be loaded from webstorage
	* @param bool $read_more If true,will display the Read me button,from where a custom readme url/text can be entered
	* @param string $read_more_url The value for the read more url if $read_more is true
	* @param string $read_more_text The value for the read more text if $read_more is true
	* @param bool $show_page_break If true will show the Page Break button
	* @param bool $show_previous_versions If true will show the Previous Versions button
	* @param string $previous_versions_code Javascript code to be executed when the previous versions button is clicked
	* @param string $width The width of the editor
	* @param string $height	 The height of the editor
	* @param bool $show_toolbar If true,will show the editor's bottom toolbar
	* @return string The html code of the editor
	*/
	public function buildEditor($name = '', $content = '', $webstorage_name = '', $webstorage_item_id = 0, $width = '100%', $height = '300px', $show_toolbar = true)
	{
		var_dump('build editor!!!');
		die;
		if (!$name) {
			$name = 'editor';
		}

		static $keep_alive = true;

		if (!$width) {
			$width = '100%';
		}
		if (!$height) {
			$height = '300px';
		}

		$this->app->plugins->run('uiBuildEditor1', $name, $content, $webstorage_name, $webstorage_item_id, $width, $height, $this);

		$html = '';
		if ($this->app->user->editor == 'wysiwyg') {
			$html = $this->buildWysiwygEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
		} elseif ($this->app->user->editor == 'bbcode') {
			$html = $this->buildBbcodeEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
		} else {
			$html = $this->buildTextareaEditor($name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, '100%', $height);
		}

		$this->app->dialogs->loadDialog('smilies', $name, ['field_name' => $name]);

		$block = new Block;
		$uploads_enabled = false;
		if ($this->app->user->uid && $this->app->usergroup->can_upload) {
			$uploads_enabled = $block->isInstalled('uploads');
		}

		$editor_data = [
			'html' => $html,
			'name' => $name,
			'width' => $width,
			'height' => $height,
			'show_toolbar' => $show_toolbar,
			'extra1' => '',
			'extra2' => '',
			'extra3' => '',
			'extra4' => '',
			'uploads_enabled' => $uploads_enabled
		];

		$editor = new Object($editor_data);
		$editor->addExtraData(['extra1', 'extra2' , 'extra3', 'extra4']);

		$this->app->plugins->run('admin_html_build_editor2', $editor);

		$this->app->theme->addVar('editor', $editor);

		$keep_alive = false;

		return $this->app->theme->getTemplate('editor');
	}

	/**
	* Builds the bbcode editor
	* @param string $name The name of the field
	* @param string $content The content of the editor
	* @param bool $keep_alive If true, will make keep alive requests
	* @param string $webstorage_name The name under which the content of the editor will be periodically saved
	* @param string $webstorage_item_id Optional ID for the $webstorage_name
	* @param string $width The width of the editor
	* @param string $height	 The height of the editor
	* @param string $class The class of the editor,if any
	* @param string $id The id of the editor,if any
	* @return string The html code of the editor
	*/
	public function buildBbcodeEditor($name = 'editor', $content = '', $keep_alive = false, $webstorage_name = '', $webstorage_item_id = 0, $width = '100%', $height = '300px', $class = '', $id = '')
	{
		var_dump('build bbcode editor!!!');
		die;
		$this->app->plugins->run('html_build_bbcode_editor1', $name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, $width, $height, $class, $id);

		if (!$id) {
			$id = $name;
		}

		$bbcode_all = false;
		$bbcode_array = [];
		if (strtolower($this->app->user->bbcode) == 'all') {
			$bbcode_all = true;
		} else {
			$bbcode_array = explode(',', $this->app->user->bbcode);
		}

		$this->app->lang->loadPackage('bbcode');

		$editor = new editors\Bbcode($name, $bbcode_array, $bbcode_all, $width, $height, $class, $id);

		if ($webstorage_name) {
			$this->app->javascript->saveEditor($id, $webstorage_name, $webstorage_item_id);
		}

		ob_start();
		$editor->output($content, $keep_alive);
		$content = ob_get_clean();

		$this->app->plugins->run('html_build_bbcode_editor2', $content);

		return $content;
	}

	/**
	* Builds the wysiwyg editor
	* @param string $name The name of the field
	* @param string $content The content of the editor
	* @param bool $keep_alive If true, will make keep alive requests
	* @param string $webstorage_name The name under which the content of the editor will be periodically saved
	* @param string $webstorage_item_id Optional ID for the $webstorage_name
	* @param string $width The width of the editor
	* @param string $height	 The height of the editor
	* @param string $class The class of the editor,if any
	* @param string $id The id of the editor,if any
	* @param array	$css_array Array with css files to be loaded inside the editor's iframe,if any
	* @return string The html code of the editor
	*/
	public function buildWysiwygEditor($name = 'venus_editor', $content = '', $keep_alive = false, $webstorage_name = '', $webstorage_item_id = 0, $width = '100%', $height = '300px', $class = '', $id = '', $css_array = [])
	{
		var_dump('build wysiwygeditor!!!');
		die;
		
		$this->app->plugins->run('html_build_wysiwyg_editor1', $name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, $width, $height, $class, $id);

		if (!$id) {
			$id = $name;
		}

		$editor = new editors\Ckeditor($name, $width, $height, $class, $id);
		$editor->css_array = $css_array;

		if ($webstorage_name) {
			$this->app->javascript->saveEditor($id, $webstorage_name, $webstorage_item_id);
		}

		ob_start();
		$editor->output($content, $keep_alive);
		$cnt = ob_get_clean();

		$this->app->plugins->run('html_build_wysiwyg_editor2', $cnt);

		return $cnt;
	}

	/**
	* Builds the textarea editor
	* @param string $name The name of the field
	* @param string $content The content of the editor
	* @param bool $keep_alive If true, will make keep alive requests
	* @param string $webstorage_name The name under which the content of the editor will be periodically saved
	* @param string $webstorage_item_id Optional ID for the $webstorage_name
	* @param string $width The width of the editor
	* @param string $height	 The height of the editor
	* @param string $class The class of the editor,if any
	* @param string $id The id of the editor,if any
	* @return string The html code of the editor
	*/
	public function buildTextareaEditor($name = 'venus_editor', $content = '', $keep_alive = false, $webstorage_name = '', $webstorage_item_id = 0, $width = '100%', $height = '200px', $class = '', $id = '')
	{
		var_dump('build textarea editor!!!');
		die;
		$this->app->plugins->run('html_build_textarea_editor1', $name, $content, $keep_alive, $webstorage_name, $webstorage_item_id, $width, $height, $class, $id);

		if (!$id) {
			$id = $name;
		}

		$editor = new editors\Textarea($name, $width, $height, $class, $id);

		if ($webstorage_name) {
			$this->app->javascript->saveEditor($id, $webstorage_name, $webstorage_item_id);
		}

		ob_start();
		$editor->output($content, $keep_alive);
		$cnt = ob_get_clean();

		$this->app->plugins->run('html_build_textarea_editor2', $cnt);

		return $cnt;
	}
}
