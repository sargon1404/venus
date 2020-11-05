<?php
/**
* The Navbar Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Navbar Class
* The functionality of the admin navbar
*/
class Navbar
{
	use \Venus\AppTrait;

	/**
	* @var bool $display True if the navbar must be displayed
	*/
	public bool $display = false;

	/**
	* @var string $title The navbar's title
	*/
	public string $title = '';

	/**
	* @var string $icon The navbar's icon
	*/
	public string $icon = '';

	/**
	* @var array $links The navbar's links
	*/
	public array $links = [];

	/**
	* @var string $links_index The index of the selected link
	*/
	public string $links_index = '';

	/**
	* @var array $buttons The navbar's buttons
	*/
	public array $buttons = [];

	/**
	* @var string $buttons_lists The redirect lists of the buttons
	*/
	protected string $buttons_lists = '';





	/**
	* @var string $url The default navbar form url
	*/
	public string $url = '';

	/**
	* @var string $ids_name The name of the ids field.
	*/
	public string $ids_name = '';

	/**
	* @var string $form_start The start of the navbar form
	*/
	protected string $form_start = '';

	/**
	* @var string $form_end The end of the navbar form
	*/
	protected string $form_end = '';

	/**
	* @var bool $form_output Determines if the navbar form is outputed
	*/
	protected bool $form_output = true;

	/**
	* @var array $form_hidden Array with extra hidden fields to place in the form
	*/
	protected array $form_hidden = [];

	/**
	* @var bool $form_display Determines if the navbar form is shown
	*/
	protected bool $form_display = false;

	/**
	* @var bool $form_display_outer Determines if the navbar outer form is shown
	*/
	protected bool $form_display_outer = false;

	/**
	* Shows the navbar
	* @return $this
	*/
	public function show()
	{
		$this->display = true;

		return $this;
	}

	/**
	* Hides the navbar
	* @return $this
	*/
	public function hide()
	{
		$this->display = false;

		return $this;
	}

	/**
	* Returns the name of the clicked navbar button [Eg: save,apply]
	* @return string The action
	*/
	/*public function getAction() : string
	{
		global $venus;
		return $this->app->request->value($this->action_name);
	}*/

	/**
	* Sets the navbar title & icon
	* @param string $title The navbar's title
	* @param string $icon The navbar's icon
	* @param bool $is_block_icon If true, will display the icon from the blocks folder of the theme
	* @return $this
	*/
	public function setTitle(string $title, string $icon = '', bool $is_block_icon = true)
	{
		$this->app->plugins->run('admin_navbar_set_title', $title, $icon, $is_block_icon, $this);

		$this->display = true;
		$this->title = $title;
		$this->icon = $icon;

		if ($is_block_icon && $icon) {
			$this->icon = $this->app->theme->images_url . 'blocks/' . $icon;
		}

		$this->app->title->set($title);

		return $this;
	}

	/**
	* Sets the navbar links
	* The links must be in the format: index => [..params]. The params are:
	* 'url' => The link's url
	* 'text' => The link's text
	* 'permission' => The required permission for the link to be visible
	* @param array $links Array containing the navbar links
	* @param string $links_index The index of the currently selected navbar link
	* @return $this
	*/
	public function setLinks(array $links, string $links_index)
	{
		$this->app->plugins->run('admin_navbar_set_links', $links, $links_index, $this);

		$this->links = $links;
		$this->links_index = $links_index;

		return $this;
	}

	/**
	* Sets the history url of the current page. It will then be shown in the history tooltip
	* @param string $title The history title
	* @param string $url The history url
	* @return $this
	*/
	public function setHistory(string $title, string $url)
	{
		if (!$title || !$url) {
			return;
		}

		$in_history = 10;
		$history = $this->app->session->get('history');
		if (!$history) {
			$history = [];
		}

		$history_array = [$title => $url];

		if ($history) {
			$i = 0;
			foreach ($history as $title => $url) {
				if ($i >= $in_history) {
					break;
				}

				$history_array[$title] = $url;
				$i++;
			}
		}

		$this->app->session->set('history', array_unique($history_array));

		return $this;
	}

	/**
	* Builds the navbar's buttons
	* The buttons must be in the format: type => [..params]. The params are:
	* 'permission' => The required permission for the button to be visible
	* 'url' => The url of the button, if any
	* 'ajax' => Can be bool or array. If true, will perform the action with an ajax call. If array, must contain the ajax options: ['element' => '', 'on_success' => '', 'on_error' => '']
	* 'tooltip' => The tooltip text to show if no ids are selected. If true, the default text is shown
	* 'icon' => The button's icon url
	* 'class' => The button's class
	* 'title' => The button's title
	* 'redirects' => [['name' => <name>, 'title' => <title>, 'icon' => <icon>]] The button's redirects
	* 'on_click' => On click code to execute; if specified, will override all other ajax settings
	* @param array $buttons The navbar buttons
	* @param string $ids_name The name of the ids fields
	* @param string $url The url to which the form points(the action). If empty $this->url is used
	* @param array $hidden_fields Array in the format name=>value with hidden fields to add to the navbar form
	* @return $this
	*/
	public function setButtons(array $buttons, string $ids_name = '', string $url = '', array $hidden_fields = [])
	{
		if (!$url) {
			$url = $this->app->url;
		}

		$this->buttons = $buttons;
		/*$this->ids_name = $ids_name;*/
		$this->url = $url;
		//$this->form_hidden = $hidden_fields;

		return $this;
	}

	/**
	* Builds the navbar form
	* @param array $buttons Array containing the navbar buttons
	* @param string $default_action The default action, applied when the user presses enter. Eg: save,apply etc..
	* @param string $url The url to which the form points(the action). If empty $this->url is used
	* @param bool $enctype_upload It true the form will have enctype="multipart/form-data"
	* @param bool $output If true, the form will be automatically outputed. If false, the caller will need to explicitly call $this->app->navbar->outputFormStart(true) and $this->app->navbar->outputFormStart(false)
	* @return $this
	*/
	/*public function setForm(array $buttons, string $default_action = '', string $url = '', bool $enctype_upload = false, bool $output = true)
	{
		if (!$url) {
			$url = $this->app->url;
		}

		$enctype = '';
		if ($enctype_upload) {
			$enctype = 'multipart/form-data';
		}

		$this->display = true;
		$this->buttons = $buttons;
		$this->form_display = true;
		$this->form_output = $output;

		$default_admin_action = $default_action;

		if ($default_action) {
			foreach ($buttons as $type => $button) {
				if ($type == $default_action) {
					$default_action = $button[1];
					break;
				}
			}
		}

		$start = $this->getFormStart($url, $default_action, $default_admin_action, $enctype);
		$end = $this->getFormEnd();

		$this->app->plugins->run('admin_navbar_set_form', $start, $end, $this);

		$this->form_start = $start;
		$this->form_end = $end;

		return $this;
	}*/



	/**
	* Builds the start of the navbar form
	* @param string $url The form's url
	* @param string $default_action The form's default action
	* @param string $default_admin_action The form's default admin action
	* @param string $enctype The form's enctype
	* @return string
	*/
	/*protected function getFormStart(string $url, string $default_action = '', string $default_admin_action = '', string $enctype = '') : string
	{
		$html = $this->app->html->formStart($url, 'admin-form', ['enctype' => $enctype]);
		$html.= $this->app->html->requestHidden($this->app->config->action_param, $default_action, 'admin-form-action');
		$html.= $this->app->html->requestHidden('tab-id', $this->app->theme->tab_id);
		$html.= $this->app->html->getToken();

		return $html;
	}*/

	/**
	* Builds the end of the navbar form
	* @return string
	*/
	/*protected function getFormEnd() : string
	{
		$html = $this->app->html->requestSubmit('', '', 'admin-form-submit');
		$html.= $this->app->html->formEnd();
		$html.= $this->getMultiForms();

		return $html;
	}*/

	/**
	* Returns the content of the multi forms
	* @return string
	*/
	/*protected function getMultiForms() : string
	{
		$html = $this->app->html->formStart('', 'admin-multi-form');
		$html.= $this->app->html->getToken();
		$html.= $this->app->html->formEnd();

		$html = $this->app->html->formStart('', 'admin-multi-ajax-form');
		$html.= $this->app->html->getToken();
		$html.= $this->app->html->getAjax();
		$html.= $this->app->html->formEnd();

		return $html;
	}*/

	/**
	* Outputs the navbar title
	*/
	public function outputTitle()
	{
		$this->title = $this->app->plugins->filter('admin_navbar_output_title', $this->title, $this);

		echo App::e($this->title);
	}

	/**
	* Outputs the navbar form start
	* @param bool $manual_output Must be set to true if this function is manually called
	*/
	/*public function outputFormStart(bool $manual_output = false)
	{
		if (!$this->form_display) {
			return;
		}
		if (!$manual_output && !$this->form_output) {
			return;
		}

		echo $this->form_start;

		$this->app->plugins->run('admin_navbar_output_form_start', $this);

		$this->form_start = '';
	}*/

	/**
	* Outputs the navbar form end
	* @param bool $manual_output Must be set to true if this function is manually called
	*/
	/*public function outputFormEnd(bool $manual_output = false)
	{
		if (!$this->form_display) {
			return;
		}
		if (!$manual_output && !$this->form_output) {
			return;
		}

		echo $this->form_end;

		$this->app->plugins->run('admin_navbar_output_form_end', $this);

		$this->form_end = '';
	}*/

	/**
	* Outputs the navbar outer form
	*/
	/*public function outputOuterForm()
	{
		if (!$this->form_display_outer || !$this->buttons || !$this->url) {
			return;
		}

		echo $this->getFormStart($this->url);

		if ($this->form_hidden) {
			foreach ($this->form_hidden as $name => $value) {
				echo $this->app->html->requestHidden($name, $value);
			}
		}

		echo $this->getFormEnd();

		$this->form_hidden = [];
	}*/

	/**
	* Adds the redirect links
	* @param string $button_id The id of the button
	* @param string $id The id of the list
	* @param array $redirects The redirects data
	*/
	protected function addRedirects(string $button_id, string $id, array $redirects)
	{
		$html = '<div id="' . eid($id) . '" class="hidden">';
		$html.= '<ul>';

		foreach ($redirects as $item) {
			$item['icon'] = $item['icon'] ?? '';

			if ($item['icon']) {
				$item['icon'] = $this->app->html->img($this->app->theme->images_url . 'buttons/redirects/' . $item['icon']);
			}

			$html.= '<li><a href="javascript:void(0)" onclick="venus.navbar.setRedirect(\'' . App::ejs($button_id) . '\', \'' . $item['name'] . '\' ,event)">' . $item['icon'] . '<span>' . App::e($item['title']) . '</span></a></li>';
		}

		$html.= '</ul>';
		$html.= '</div>';

		$this->buttons_lists.= $html;
	}

	/**
	* Outputs the navbar links
	*/
	public function outputLinks()
	{
		$this->links = $this->app->plugins->filter('admin_navbar_output_links_links', $this->links, $this);

		echo '<ul>' . "\n";
		echo '<li>' . "\n";
		echo '<a href="javascript:void(0)" id="history" onclick="venus.navbar.openHistory(this, event)">' . App::__('history') . '</a>';
		echo '</li>' . "\n";

		if ($this->links) {
			$html = '';
			foreach ($this->links as $index => $link) {
				if (!empty($link['permission'])) {
					if (!$this->app->user->can($link['permission'])) {
						continue;
					}
				}

				$link['class'] = '';
				if ($index == $this->links_index) {
					$link['class'] = 'selected';
				}

				$html.= '<li>' . $this->app->html->a($link['url'], $link['title'], ['class' => $link['class']]) . '</li>';
			}

			$html = $this->app->plugins->filter('admin_navbar_output_links_html', $html, $this->links, $this);

			echo $html;
		}

		echo '</ul>';
		echo $this->outputHistoryLinks();

		$this->links_array = [];
	}

	/**
	* Outputs the history link
	*/
	public function outputHistoryLinks()
	{
		$history = $this->app->session->get('history');

		$history = $this->app->plugins->filter('admin_navbar_output_history_links_links', $history, $this);

		$html = '<div id="history-list" class="hidden">';

		if ($history) {
			$html.= '<ul>';
			foreach ($history as $title => $url) {
				$html.= '<li>' . $this->app->html->a($url, $title) . '</li>';
			}
			$html.= '</ul>';
		}

		$html.= '</div>';

		$html = $this->app->plugins->filter('admin_navbar_output_history_links_html', $html, $history, $this);

		echo $html;
	}

	/**
	* Outputs the navbar buttons
	*/
	public function outputButtons()
	{
		$this->buttons = $this->app->plugins->filter('admin_navbar_output_buttons', $this->buttons, $this);

		if (!$this->buttons) {
			return;
		}

		$html = '<div id="navbar-buttons-loading"></div>' . "\n";
		$html.= '<div id="navbar-buttons-list">' . "\n";
		$html.= '<ul>' . "\n";

		$index = 0;
		foreach ($this->buttons as $type => $button) {
			if (!empty($button['permission'])) {
				if (!$this->app->user->hasPermission($button['permission'])) {
					continue;
				}
			}

			$index++;

			$id = 'navbar-button-' . $type;
			$url = $button['url'] ?? '';
			$title = $button['title'] ?? App::__('button_' . $type);
			$tooltip = $button['tooltip'] ?? false;
			$icon_url = $button['icon'] ?? $this->app->theme->images_url . 'buttons/' . $type . '.png';
			$icon = $this->app->html->img($icon_url, 0, 0, $title);
			$onclick = $button['on_click'] ?? '';
			$redirects = $button['redirects'] ?? '';

			if ($tooltip === true) {
				$tooltip = App::__('button_select_item');
			}

			if ($onclick) {
				//add the params for the onclick event, if not specified
				$params = '';
				if (!str_contains($onclick, '(')) {
					$params = "('{$type}', this)";
				}

				$onclick = "return {$onclick}{$params}";
				;
			} elseif (!$url) {
				//is this an ajax button?
				$ajax = $button['ajax'] ?? false;

				if ($ajax) {
					if (is_array($ajax)) {
						$ajax = $this->app->javascript->toItem($$ajax, true, ['on_success', 'on_error']);
					}

					$onclick = "venus.navbar.submit('{$type}', {$ajax}, '{$this->ids_name}', this, event)";
				}
			}

			if ($redirects) {
				$redirects_id = $id . '-list';
				$this->addRedirects($id, $redirects_id, $redirects);

				$redirects = '<span class="navbar-button-redirect" onclick="venus.navbar.openRedirect(\'' . App::ejs($redirects_id) . '\', this, event)"></span>';
			}

			$attributes = [
				'id' => $id,
				'class' => $button['class'] ?? '',
				'data-tooltip-error' => $tooltip,
				'onclick' => [$onclick],
			];

			$html.= '<li>' . $this->app->html->a($url, $icon . $title, $attributes, false) . '</li>';
		}

		if (!$index) {
			return '';
		}

		$html.= '</ul>' . "\n";
		$html.= '</div>' . "\n";
		$html.= '<div id="navbar-buttons-redirects">' . "\n";
		$html.= $this->buttons_lists;
		$html.= '</div>' . "\n";
		$html.= '<div class="clear"></div>' . "\n";

		$html = $this->app->plugins->filter('admin_navbar_output_buttons_html', $html, $this->buttons, $this);

		echo $html;

		$this->buttons = [];
	}
}
