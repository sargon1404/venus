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
	* @param string $item_id The id of the item associated with this action list
	* @param array $links Array with the entries for the quick action list. Each entry must be in the format: action => [param1 => 'value', param2 => 'value']
	* @return string The html code
	*/
	public function getList(string $item_id, array $links) : string
	{
		if (!$links) {
			return '';
		}

		$html = '<div class="actions-list" id="item-actions-list-' . $item_id . '">' . "\n";
		$html.= '<ul class="list">' . "\n";

		$index = 0;
		foreach ($links as $action => $link) {
			if (!empty($link['permission'])) {
				if (!$this->app->user->can($link['permission'], $item_id)) {
					continue;
				}
			}

			$link['ajax'] = $link['ajax'] ?? false;
			$link['icon'] = $link['icon'] ?? '';
			$link['tooltip'] = $link['tooltip'] ?? '';
			$link['alt'] = $link['alt'] ?? (isset($link['tooltip']) ? App::__($link['tooltip']) : $action);
			$link['redirect'] = $link['redirect'] ?? 0;
			$link['on_click'] = $link['on_click'] ?? '';

			if ($link['icon']) {
				if (!$this->app->uri->isUrl($link['icon'])) {
					$link['icon'] = $this->app->theme->images_url . 'buttons/' . $link['icon'] . '_small.png';
				}
			} else {
				$link['icon'] = $this->app->theme->images_url . 'buttons/' . $action . '_small.png';
			}

			if ($link['tooltip']) {
				$link['tooltip'] = ' data-tooltip="' . App::__(nl2br(App::__e($link['tooltip']))) . '"';
			}

			$action = App::ejs($action);
			$item_id = App::ejs($item_id);

			$index++;

			if ($link['on_click']) {
				//custom on click specified
				$params = '';
				if (!str_contains($button['on_click'], '(')) {
					$params = "('{$item_id}', '{$action}', this)";
				}

				$link['on_click'] = "onclick=\"return {$link['on_click']}{$params}\"";
			} else {
				if (is_array($link['ajax'])) {
					$link['ajax']= $this->app->javascript->toItem($link['ajax'], true, ['on_success', 'on_error']);
				} else {
					$link['ajax'] = (int)$link['ajax'];
				}

				$link['url'] = App::__($link['url']);
				$link['on_click'] = " onclick=\"return venus.ui.doListAction('{$item_id}', '{$action}', {$link['ajax']}, {$link['redirect']}, this)\"";
			}

			$html.= '<li><a href="' . $link['url'] . '"' . $link['on_click'] . $link['tooltip'] . '><img src="' . App::__($link['icon']) . '" alt="' . App::__($link['alt']) . '" /></a></li>' . "\n";
		}

		$html.= '</ul>' . "\n";
		$html.= '</div>' . "\n";

		if (!$index) {
			return '';
		}

		return $html;
	}

	/**
	* Builds a drop down with the options
	* The supported params for $options are:
	* 'permission' => The permission, if any, the user must have to have the option displayed
	* 'text' => text = The text of the option
	* 'ajax' => Can be bool or array. If true, will perform the action with an ajax call. If array, must contain the ajax options: ['element' => '', 'on_success' => '', 'on_error' => '']
	* @param string $item_id The id of the item associated with this form action
	* @param array $options The options
	* @param string $url The form's url (action). If empty $this->app->url is used
	* @param string $item_id_name The name of the hidden input where the $item_id is stored
	* @param string $item_ids_name The name of the hidden input where the $item_ids is stored
	* @return string The html code
	*/
	public function getForm(string $item_id, array $options, string $url = '', string $item_id_name = 'id', string $item_ids_name = 'ids') : string
	{
		if (!$url) {
			$url = $this->app->url;
		}

		$ajax_options = '';
		$options_array = [];
		$form_id = 'actions-form-' . $item_id;
		$select_id = 'actions-select-' . $item_id;

		foreach ($options as $action => $option) {
			if (!empty($option['permission'])) {
				if (!$this->app->user->can($option['permission'], $item_id)) {
					continue;
				}
			}

			$option['text'] = App::__($option['text']);
			$option['ajax'] = $option['ajax'] ?? false;
			if ($option['ajax']) {
				$option['ajax'] = 1;
			}

			if ($option['ajax']) {
				if (is_array($option['ajax'])) {
					$ajax_key = App::ejs($item_id . '-' . $action);
					$ajax_options.= 'venus.ui.form_action_ajax[\'' . $ajax_key . '\'] = ' . $this->app->javascript->toObject($option['ajax'], true, ['on_success', 'on_error']) . ';' . "\n";

					$option['ajax'] = 1;
				}
			}

			$options_array[$option['text']] = ['value' => App::__($action), 'data-ajax' => $option['ajax']];
		}

		if (!$options_array) {
			return '';
		}

		$html = '<div class="actions-form">' . "\n";
		$html.= $this->app->html->formOpen($url, ['id' => $form_id, 'onsubmit' => ["return venus.ui.formAction('{$item_id}', '{$form_id}', '{$select_id}')"]]);
		$html.= $this->app->html->getToken();
		$html.= $this->app->html->inputHidden($item_id_name, $item_id);
		$html.= $this->app->html->inputHidden($item_ids_name . '[]', $item_id);
		$html.= $this->app->html->selectOpen($this->app->config->action_param, ['id' => $select_id, 'onchange' => ["venus.html.submitForm('{$form_id}')"], 'form' => $form_id]);
		$html.= $this->app->html->options($options_array);
		$html.= $this->app->html->selectClose();
		$html.= '&nbsp;';
		$html.= $this->app->html->button(App::__('go'));
		$html.= $this->app->html->formClose();
		$html.= '</div>';
		if ($ajax_options) {
			$html.= '<script>';
			$html.= $ajax_options;
			$html.= '</script>';
		}

		return $html;
	}
}
