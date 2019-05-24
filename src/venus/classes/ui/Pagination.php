<?php
/**
* The Pagination Class
* @package Venus
*/

namespace Venus\Ui;

use Venus\App;

/**
* The Pagination Class
* Generates pagination links
*/
class Pagination extends \Mars\Ui\Pagination
{
	/**
	* Builds the pagination template. The number of pages is computed as $total_items / $items_per_page.
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param string $current_page The current page
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items per page
	* @param string $is_seo_url If true will try to replace $seo_page_param from $base_url with the page number rather than append the page number as a param
	* @param bool $use_ajax If true, will load the pages using ajax
	* @param string $update_element_id The id of the element which will be updated when the page changes
	* @param string $js_function function to be executed, if any, when the page changes
	* @return string The html code
	*/
	public function get(string $base_url, int $current_page, int $total_items, int $items_per_page = 30, bool $is_seo_url = false, bool $use_ajax = false, string $update_element_id = '', string $js_function = '') : string
	{
		if (!$total_items) {
			return '';
		}

		$pages_count = $this->getPagesCount($total_items, $items_per_page);
		if ($pages_count == 1) {
			return '';
		}

		$url_extra = [];
		$current_page = $this->getCurrentPage($current_page, $pages_count);
		$replace_seo_page = $this->canReplaceSeoParam($base_url, $is_seo_url);
		$data = $this->getLimits($current_page, $pages_count);

		$start = $data['start'];
		$end = $data['end'];

		$js_update_element_id = '';
		if ($use_ajax) {
			$js_update_element_id = App::ejs($update_element_id);
		}

		$this->app->plugins->run('uiPaginationGet1', $base_url, $current_page, $total_items, $items_per_page, $is_seo_url, $use_ajax, $update_element_id, $js_function, $this);

		$url_extra = [
			'use_ajax' => $use_ajax,
			'js_function' => $js_function,
			'js_update_element_id' => $js_update_element_id
		];

		$pages = $this->getPages($base_url, $start, $end, $current_page, $replace_seo_page, $url_extra);
		$previous = $this->getPreviousLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$next = $this->getNextLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$first = $this->getFirstLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$last = $this->getLastLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$jump = $this->getJumpToLink($base_url, $current_page, $pages_count, $is_seo_url, $url_extra);

		$pagination_data = [
			'current_page' => $current_page,
			'total_items' => $total_items,
			'items_per_page' => $items_per_page,
			'start' => $start,
			'end' => $end,
			'previous' => $previous,
			'next' => $next,
			'first' => $first,
			'last' => $last,
			'jump' => $jump,
			'pages_count' => $pages_count,
			'pages' => $pages
		];

		$content = $this->getTemplate($pagination_data);

		$this->app->plugins->run('uiPaginationGet2', $content, $pagination_data, $this);

		return $content;
	}

	/**
	* @see \Mars\Ui\Pagination::getUrl()
	* {@inheritDoc}
	*/
	protected function getUrl(string $base_url, int $page, bool $replace_seo_page = false, array $url_extra = []) : string
	{
		$use_ajax = $url_extra['use_ajax'];
		$js_function = $url_extra['js_function'];
		$js_update_element_id = $url_extra['js_update_element_id'];

		if (!$use_ajax) {
			$url = '';
			if (!$replace_seo_page) { 
				//build the url, by appending the page as a query string
				$url = $this->app->uri->build($base_url, [$this->page_param => $page]);
			} else { 
				//replace the seo page param with the page number
				$url = str_replace($this->seo_page_param, $page, $base_url);
			}

			return $url;
		} else {
			//use ajax
			$url = '';
			if (!$replace_seo_page) {
				$url = $this->app->uri->build($base_url, [$this->page_param => $page, $this->app->config->response_param => 'ajax']);
			} else {
				$url = $this->app->uri->build(str_replace($this->seo_page_param, $page, $base_url), [$this->app->config->response_param => 'ajax']);
			}

			$url = App::ejs($url, false);

			if (!$js_function) {
				$is_dialog = 0;
				if ($this->app->type == 'dialog') {
					$is_dialog = 1;
				}

				return "javascript:venus.ui.pagination_update('{$url}', '{$js_update_element_id}', {$is_dialog})";
			} else {
				return "javascript:venus.ui.pagination_update_func('{$url}', '{$js_update_element_id}', '{$js_function}');";
			}
		}
	}

	/**
	* @see \Mars\Ui\Pagination::getJumpToLink()
	* {@inheritDoc}
	*/
	protected function getJumpToLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		$max_links = $this->max_links;

		if (!$max_links || $max_links >= $pages_count) {
			return ['show' => false, 'url' => '', 'page' => ''];
		}

		$jump_url = $base_url;
		if ($is_seo_url) {
			$jump_url = str_replace($this->app->config->seo_page_param, '', $jump_url);
		}

		$jump_form = '<form method="post" action="' . App::e($jump_url) . '" onsubmit="return venus.ui.pagination_jump(this,\'' . App::ejs($this->page_param) . '\',' . $pages_count . ')">';
		$jump_form.= '<input type="text" name="venus-pagination-jump" value="" class="small" />&nbsp;<input type="submit" value="' . App::__('pagination_jump') . '" />';
		$jump_form.= '</form>';

		return ['show' => true, 'form' => $jump_form];
	}
}
