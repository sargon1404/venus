<?php
/**
* The Actions Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Actions Class
* Item actions
*/
class Actions
{
	use \Venus\AppTrait;

	/**
	* @var string $forms The html code of the generated forms
	*/
	protected $forms = '';

	/**
	* Builds an actions list from $links
	* The supported params for $links are:
	* 'permission' => The permission, if any, the user must have to have the link displayed
	* 'url' => The url of the link, if any
	* 'ajax' => Can be bool or array. If true, will perform the action with an ajax call. If array, must contain the ajax options: ['element' => '', 'on_success' => '', 'on_error' => '']
	* 'alt' => The alt tag of the icon image
	* 'icon' => The icon url
	* 'tooltip' => The tooltip text
	* 'redirect' => If true, will redirect to url, without any extra processing
	* 'on_click' => On click code to execute; if specified, will override all other ajax settings
	* @param string $item_id The id of the item associated with this quick action list
	* @param array $links Array with the entries for the quick action list. Each entry must be in the format: action => [param1 => 'value', param2 => 'value']
	* @return string The html code
	*/
	public function getList(string $item_id, array $links) : string
	{
		if (!$links) {
			return '';
		}

		$item_id = $this->escape->id($item_id);

		$html = '<div class="actions-list" id="item-quick-action-' . $item_id . '">' . "\n";
		$html.= '<ul class="list">' . "\n";

		$index = 0;
		foreach ($links as $action => $link) {
			if (!empty($link['permission'])) {
				if (!$this->app->user->can($link['permission'], $item_id)) {
					continue;
				}
			}

			$link['ajax'] = $link['ajax'] ?? false;
			$link['alt'] = $link['alt'] ?? $action;
			$link['icon'] = $link['icon'] ?? '';
			$link['tooltip'] = $link['tooltip'] ?? '';
			$link['redirect'] = (int)$link['icon'] ?? 0;
			$link['on_click'] = $link['on_click'] ?? '';

			if (!$link['icon']) {
				$link['icon'] = $this->app->theme->images_url . 'buttons/' . $action . '_small.png';
			}
			if ($link['tooltip']) {
				$link['tooltip'] = ' data-tooltip="' . App::e(nl2br(App::estr($link['tooltip']))) . '"';
			}

			$action = App::ejs($action);
			$item_id = App::ejs($item_id);

			$index++;

			if ($link['on_click']) {
				//custom on click specified
				$params = '';
				if (strpos($button['on_click'], '(') === false) {
					$params = "('{$item_id}', '{$action}', this)";
				}

				$link['on_click'] = "onclick=\"return {$link['on_click']}{$params}\"";
			} else {
				if (is_array($link['ajax'])) {
					$link['ajax']= $this->app->javascript->toItem($link['ajax'], true, ['on_success', 'on_error']);
				} else {
					$link['ajax'] = (int)$link['ajax'];
				}

				$link['url'] = App::e($link['url']);
				$link['on_click'] = " onclick=\"return venus.ui.quick_action('{$item_id}', '{$action}', {$link['ajax']}, {$link['redirect']}, this)\"";
			}

			$html.= '<li><a href="' . $link['url'] . '"' . $link['on_click'] . $link['tooltip'] . '><img src="' . App::e($link['icon']) . '" alt="' . App::e($link['alt']) . '" /></a></li>' . "\n";
		}

		$html.= '</ul>' . "\n";
		$html.= '</div>' . "\n";

		if (!$index) {
			return '';
		}

		return $html;
	}

	protected function addSelectForm($item_id, $item_id_name, $item_ids_name, $url, $form_id, $select_id)
	{
		$html = $this->app->html->formStart($url, $form_id, ['onsubmit' => ["return venus.ui.form_action('{$item_id}', '{$form_id}', '{$select_id}')"]]);
		$html.= $this->app->html->getToken();
		$html.= $this->app->html->requestHidden($item_id_name, $item_id);
		$html.= $this->app->html->requestHidden($item_ids_name . '[]', $item_id);
		$html.= $this->app->html->formEnd();
	}

	/**
	* Builds a drop down with the options
	* The supported params for $options are:
	* 'permission' => The permission, if any, the user must have to have the option displayed
	* 'text' => text = The text of the option
	* 'ajax' => Can be bool or array. If true, will perform the action with an ajax call. If array, must contain the ajax options: ['element' => '', 'on_success' => '', 'on_error' => '']
	* @param int $item_id The id of the item associated with this form action
	* @param string $item_id_name The name of the hidden input where the $item_id is stored
	* @param string $item_ids_name The name of the hidden input where the $item_ids is stored
	* @param array $options The options of the form. Each element of the array must be in the format: [action, ajax, permission, text]
	* @param string $url The form's url (action). If empty $this->app->url is used
	* @return string The html code
	*/
	public function getSelect($item_id, $item_id_name, $item_ids_name, $options, $url = '') : string
	{
		if (!$options) {
			return '';
		}

		if (!$url) {
			$url = $this->app->url;
		}

		$item_id = $this->app->escape->id($item_id);
		$form_id = 'actions-form-' . $item_id;
		$select_id = 'actions-select-' . $item_id;

		$html = '<div class="actions-select">' . "\n";
		$html.= $this->app->html->selectStart($this->app->config->action_param, $select_id, ['onchange' => ["venus.html.submit_form('{$form_id}')"], 'form' => $form_id]);

		$index = 0;
		$ajax_options = '';

		foreach ($options as $action => $option) {
			if (!empty($option['permission'])) {
				if (!$this->app->user->can($option['permission'], $item_id)) {
					continue;
				}
			}

			$index++;

			if ($option['ajax']) {
				if (is_array($option['ajax'])) {
					$ajax_key = App::ejs($item_id . '-' . $action);
					$ajax_options.= 'venus.ui.form_action_ajax[\'' . $ajax_key . '\'] = ' . $this->app->javascript->toItem($option['ajax'], true, ['on_success', 'on_error']) . ';' . "\n";

					$option['ajax'] = 1;
				} else {
					$option['ajax'] = (int)$option['ajax'];
				}
			}

			$html.= '<option value="' . App::e($action) . '" data-ajax="' . $option['ajax'] . '">' . App::estr($option['text']) . '</option>' . "\n";
		}
		if (!$index) {
			return '';
		}

		$html.= $this->app->html->selectEnd();

		$html.= '&nbsp;';
		$html.= $this->app->html->requestButton('button', App::__('go'));

		if ($ajax_options) {
			$html.= '<script type="text/javascript">';
			$html.= $ajax_options;
			$html.= '</script>';
		}

		$html.= '</div>';

		return $html;
	}
}
