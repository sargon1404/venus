<?php
/**
* The Sql Builder Class.
* @package Venus
*/

namespace Venus;

/**
* The Sql Builder Class.
* Builds sql code
*/
class Sql extends \Mars\Sql
{
	/**
	* Returns a LIMIT statement corresponding to the current page
	* @param int $page The current page
	* @param int $page_items Items per page
	* @param int $total_items The total number of items. Optional.
	* @param string $page_param_name The page param. If empty $this->app->config->page_param is used
	* @return $this
	*/
	public function pageLimit(int $page = 0, int $page_items = 0, int $total_items = 0)
	{
		if (!$page_items) {
			$page_items = $this->app->config->items_per_page;
		}
		if (!$page) {
			if (!$page_param_name) {
				$page_param_name = $this->app->config->page_param;
			}

			$page = $this->app->request->getPage($page_param_name);
		}

		return parent::pageLimit($page, $page_items, $total_items);
	}
}
