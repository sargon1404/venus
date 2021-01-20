<?php
/**
* The Uri Class
* @package Venus
*/

namespace Venus;

/**
* The Uri Class
* Functionality for building & handling urls
*/
class Uri extends \Mars\Uri
{
	/**
	* @var array $blocks The name of the blocks used for login/register etc..
	*/
	public array $block_names = [
		'login' => 'login',
		'register' => 'register',
		'profile' => 'profile',
		'account' => 'account',
		'pms' => 'private_messages',
		'search' => 'search'
	];

	/**
	* @var array $cached_data Array containing blocks/categories data read from the cache
	*/
	protected array $cached_data = ['blocks' => null, 'categories' => null];

	/**
	* @var array $preloaded_data Array storing the preloaded users/pages/tags data
	*/
	protected array $preloaded_data = ['users' => [], 'pages' => [], 'tags' => []];

	/**
	* Builds an url appending $params to $base_url
	* @param string $base_url The url to which params will be appended. If empty, $this->app->index will be used
	* @param array $params Array containing the values to be appended. Specified as $name=>$value
	* @param bool $remove_empty_params If true, will remove from the query params the params with value = ''
	* @return string The url
	*/
	public function build(string $base_url = '', array $params = [], bool $remove_empty_params = true) : string
	{
		if (!$base_url) {
			$base_url = $this->app->index;
		}
		if (!$params) {
			return $base_url;
		}

		return parent::build($base_url, $params, $remove_empty_params);
	}

	/**
	* Adds $action as a param to $base_url
	* @param string $base_url The url to which action will be appended
	* @param string $action The action
	* @return string The url
	*/
	public function addAction(string $base_url, string $action) : string
	{
		$params = [$this->app->config->action_param => $action];

		return $this->build($base_url, $params);
	}

	/**
	* Adds the response=ajax param to $base_url
	* @param string $base_url The url to the params will be appended
	* @param string $response_param The response param. If empty, $this->app->config->response_param is used
	* @return string The url
	*/
	public function addAjax(string $base_url, string $response_param = '') : string
	{
		if (!$response_param) {
			$response_param = $this->app->config->response_param;
		}

		return parent::addAjax($base_url, $response_param);
	}

	/**
	* Determines if $url is a local url
	* @param string $url The url
	* @return bool True if the url is local
	*/
	public function isLocal(string $url) : bool
	{
		$url2 = str_replace(['https:', 'http:'], '', strtolower($url));
		$site_url = str_replace(['https:', 'http:'], '', strtolower($this->app->url));

		if (!str_starts_with($url2, $site_url)) {
			return false;
		}

		return true;
	}

	/**
	* Returns true, if $url is actually an url (starts with http:// https://)
	* @param string $url The url
	* @return bool
	*/
	public function isUrl(string $url) : bool
	{
		$url = trim($url);

		if (str_starts_with($url, 'http//:') || str_starts_with($url, 'https//:')) {
			return true;
		}

		return false;
	}

	/**
	* Returns true, if $url is actually a javascript action
	* @param string $url The url
	* @return bool
	*/
	public function isJavascript(string $url) : bool
	{
		$url = trim($url);

		if (str_starts_with($url, 'javascript:') === 0) {
			return true;
		}

		return false;
	}

	/**
	* Returns the url of the login block.
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	public function getLogin(string $action = '', array $params = []) : string
	{
		return $this->getBlockUrl('login', $action, $params);
	}

	/**
	* Returns the url of the register block.
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	public function getRegister(string $action = '', array $params = []) : string
	{
		return $this->getBlockUrl('register', $action, $params);
	}

	/**
	* Returns the url of the logout link.
	* @return string The url
	*/
	public function getLogout() : string
	{
		return $this->getBlockUrl('login', 'logout');
	}

	/**
	* Returns the url of the private messages block.
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	public function getPrivateMessages(string $action = '', array $params = []) : string
	{
		return $this->getBlockUrl('pms', $action, $params);
	}

	/**
	* Returns the url of the account block of the current user
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	public function getAccount(string $action = '', array $params = []) : string
	{
		return $this->getBlockUrl('account', $action, $params);
	}

	/**
	* Returns the url of the profile block of the current user
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	public function getProfile(string $action = '', array $params = []) : string
	{
		return $this->getBlockUrl('profile', $action, $params);
	}

	/**
	* Returns the url of a special [login|register etc..] block
	* @param string $block The name of the block
	* @param string $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @return string The url
	*/
	protected function getBlockUrl(string $block, string $action = '', array $params = []) : string
	{
		$url = $this->getBlock($this->block_names[$block], $action, $params);

		$this->app->plugins->run('uri_get_block_url', $block, $url, $this);

		return $url;
	}

	/**
	* Builds a document's url
	* @param string $type The document's type
	* @param string $format The url's format
	* @param object $item The document item
	* @param int $id The document's id
	* @param array $params Array containing the params to append to the url
	* @param array $seo_extra Extra seo parts to be inclued in the url
	* @param int $page_no The page number, if any
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @param bool $has_category If true, will include the item's category in the url
	* @param bool $has_parents If true, will include the item's parent in the url
	* @return string The url
	*/
	protected function getDocumentUrl(string $type, string $format, object $item, int $id, array $params, array $seo_extra, int $page_no = 0, bool $return_base_url = false, bool $has_category = true, bool $has_parents = false) : string
	{
		if (!$id) {
			return $this->getEmpty();
		}

		$url = '';

		if (!$this->app->config->seo_enable) {
			//seo is not enabled; append the params as query vars
			if ($page_no > 0) {
				$params[$this->app->config->page_param] = $page_no;
			}

			$url = $this->build($this->app->index, [$this->app->config->type_param => $type, $this->app->config->id_param => $id] + $params);
		} else {
			$url = $this->app->url;

			if ($item->seo_slug) {
				//does the item has a seo slug set? If so, use it
				$url.= $item->seo_slug;

				if ($this->app->config->seo_slug_slash) {
					$url.= '/';
				}

				if ($page_no) {
					$params[$this->app->config->page_param] = $page_no;
				}
			} else {
				//build the sef url
				$extra = '';
				$parents_param = '';
				$category_param = '';
				$page_param = '';

				//build the extra seo slugs, if any
				if ($seo_extra) {
					if (!is_array($seo_extra)) {
						$seo_extra = [$seo_extra];
					}

					$extra = '/' . implode('/', $seo_extra);
				}

				//get the data for the block's category, if any
				if ($has_category && $item->category) {
					$this->loadCachedData('categories', 'categories');
					$category_data = $this->getCachedData('categories', $item->category);

					if ($category_data) {
						$category_param = $category_data->seo_alias . '/';
					}
				}

				//include the item's parents, if any
				if ($has_parents && $item->parents) {
					$parents_param = $item->parents . '/';
				}

				//get the page param
				if ($return_base_url) {
					$page_param = $this->app->config->seo_page_param;
				} elseif ($page_no) {
					$page_param = str_replace('{PAGE_NO}', $page_no, $this->app->config->seo_page_param);
				}

				//replace the category/page/extra params etc...
				$search = ['{CATEGORY_PARAM}', '{PARENTS_PARAM}' , '{ALIAS}', '{PAGE_PARAM}', '{ID}', '{EXTRA}'];
				$replace = [$category_param, $parents_param, $item->seo_alias, $page_param, $id, $extra];

				$url.= str_replace($search, $replace, $format);
			}

			//append the params
			if ($params) {
				$url = $this->build($url, $params);
			}
		}

		return $url;
	}

	/**
	* Returns the url of a block
	* @param int|string|array|object $block The block's id (int) or the block's name (string) or the block's data (array/object)
	* @param int $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @param array $seo_extra Extra seo parts to be inclued in the url
	* @param int $page_no The page number, if any
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @return string The url
	*/
	public function getBlock(int|string|array|object $block, string $action = '', array $params = [], array $seo_extra = [], int $page_no = 0, bool $return_base_url = false) : string
	{
		$block = $this->getBlockData($block);
		if (!$block) {
			return $this->getEmpty();
		}

		if ($action) {
			$params = [$this->app->config->action_param => $action] + $params;
		}

		$url = $this->getDocumentUrl('block', $this->app->config->seo_block_url, $block, $block->bid, $params, $seo_extra, $page_no, $return_base_url);

		$this->app->plugins->run('uri_get_block', $url, $block, $action, $params, $seo_extra, $page_no, $return_base_url, $this);

		return $url;
	}

	/**
	* Returns the url of the current block
	* @param int $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @param array $seo_extra Extra seo parts to be inclued in the url
	* @param int $page_no The page number, if any
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @return string The url
	*/
	public function getCurrentBlock(string $action = '', array $params = [], array $seo_extra = [], int $page_no = 0, bool $return_base_url = false) : string
	{
		return $this->getBlock($this->app->document->name, $action, $params, $seo_extra, $page_no, $return_base_url);
	}

	/**
	* Returns the block's data
	* @param int|string|array|object $block The block's id (int) or the block's name (string) or the block's data (array/object).
	* @return object The block object
	*/
	protected function getBlockData(int|string|array|object $block) : ?object
	{
		if (!$block) {
			return null;
		}

		if (is_array($block)) {
			$block = new Item($block);
		} elseif (!is_object($block)) {
			$this->loadCachedData('blocks', 'blocks');

			//a block id was passed
			if ((int)$block) {
				$block = $this->findInCachedData('blocks', 'bid', $block);
			} else { //a block name was passed
				$block = $this->getCachedData('blocks', $block);
			}
		}

		return $block;
	}

	/**
	* Returns the url of a page
	* @param int|array|object $page The page's id (int) or the page's data (array, object). If data, must include fields: id,category,seo_alias,seo_slug
	* @param int $page_no The page's page number
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @return string The url
	*/
	public function getPage(int|array|object $page, int $page_no = 0, bool $return_base_url = false) : string
	{
		$page = $this->getPageData($page);
		if (!$page) {
			return $this->getEmpty();
		}

		$url = $this->getDocumentUrl('page', $this->app->config->seo_page_url, $page, $page->pid, [], [], $page_no, $return_base_url);

		$this->app->plugins->run('uri_get_page', $url, $page, $page_no, $return_base_url, $this);

		return $url;
	}

	/**
	* Returns the block's data
	* @param int|array|object $page The page's id (int) or the page's data (array, object). If data, must include fields: id,category,seo_alias,seo_slug if array
	* @return object The page object
	*/
	protected function getPageData(int|array|object$page) : ?object
	{
		if (!$page) {
			return null;
		}

		if ($page instanceof \Venus\Object\Page) {
			$page = $page->getData(['pid', 'category', 'seo_alias', 'seo_slug']);
		}

		if (is_array($page)) {
			$page = new Item($page);
		} elseif (!is_object($page)) {
			$pid = (int)$page;
			if (!$pid) {
				return null;
			}

			//was the page data preloaded? If not, load it from the db
			$page = $this->getPreloadedData('pages', $pid);
			if (!$page) {
				$page = $this->app->db->selectById('venus_pages', $pid, 'pid, category, seo_alias, seo_slug');
			}
		}

		return $page;
	}

	/**
	* Returns the url of a category
	* @param int|array|object $category Either the category's id or the category's data (array/object). If data, must include fields: id,category_seo_alias,category_seo_slug
	* @param int $page_no The category's page number
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @return string The url
	*/
	public function getCategory(int|array|object $category, int $page_no = 0, bool $return_base_url = false) : string
	{
		$category = $this->getCategoryData($category);
		if (!$category) {
			return $this->getEmpty();
		}

		$url = '';
		if ($category->cid == App::CATEGORIES['homepage']) {
			$url = $this->app->url;
		} else {
			$url = $this->getDocumentUrl('category', $this->app->config->seo_category_url, $category, $category->cid, [], [], $page_no, $return_base_url, false, true);
		}

		$this->app->plugins->run('uri_get_category', $url, $category, $page_no, $return_base_url, $this);

		return $url;
	}

	/**
	* Returns the category's data
	* @param int|array|object $category Either the category's id or the category's data (array, object). If data, must include fields: id,category_seo_alias,category_seo_slug
	* @return object The category object
	*/
	protected function getCategoryData(int|array|object $category) : ?object
	{
		if (!$category) {
			return null;
		}

		if ($category instanceof \Venus\Object\Category) {
			$category = $category->getData(['cid', 'seo_alias', 'seo_slug']);
		}

		if (is_array($category)) {
			$category = new Item($category);
		} elseif (!is_object($category)) {
			$cid = (int)$category;
			if (!$cid) {
				return null;
			}

			$this->loadCachedData('categories', 'categories');

			$category = $this->getCachedData('categories', $cid);
		}

		return $category;
	}

	/**
	* Returns the url of a tag
	* @param int|array|object $tag Either the tag's data (array/object) or the tag's ID. If data, must include fields: id,tag_seo_alias,tag_seo_slug
	* @param int $page_no The tag's page number
	* @param bool $return_base_url If true, will return the base url, with the page number *not* replaced. Usefull when building pagination urls
	* @return string The url
	*/
	public function getTag(int|array|object $tag, int $page_no = 0, bool $return_base_url = false) : string
	{
		$tag = $this->getTagData($tag);
		if (!$tag) {
			return $this->getEmpty();
		}

		$url = $this->getDocumentUrl('tag', $this->app->config->seo_tag_url, $tag, $tag->tid, [], [], $page_no, $return_base_url, false);

		$this->app->plugins->run('uri_get_tag', $url, $tag, $page_no, $return_base_url, $this);

		return $url;
	}

	/**
	* Returns the tag's data
	* @param int|array|object $tag Either the tag's data (array/object) or the tag's ID. If data, must include fields: id,tag_seo_alias,tag_seo_slug
	* @return object The tag object
	*/
	protected function getTagData(int|array|object $tag) : ?object
	{
		if (!$tag) {
			return null;
		}

		if ($tag instanceof \Venus\Object\Tag) {
			$tag = $tag->getData(['tid', 'seo_alias', 'seo_slug']);
		}

		if (is_array($tag)) {
			$tag = new Item($tag);
		} elseif (!is_object($tag)) {
			$tid = (int)$tag;
			if (!$tid) {
				return null;
			}

			$tag = $this->getPreloadedData('tags', $tid);
			if (!$tag) {
				$tag = $this->app->db->selectById('venus_tags', $tid, 'tid, seo_alias, seo_slug');
			}
		}

		return $tag;
	}

	/**
	* Returns the url of a user's profile
	* @param int|array|object $user Either the users's data (array/object) or the user's ID. If data, these fields must be included: id,username,seo_alias
	* @return string The url
	*/
	public function getUser(int|array|object $user) : string
	{
		$user = $this->getUserData($user);
		if (!$user || !$user->id) {
			return $this->getEmpty();
		}

		if (!$this->app->config->seo_enable) {
			$url = $this->build($this->app->index, [$this->app->config->type_param => 'user', 'id' => $user->id]);
		} else {
			$url = $this->app->url;

			$search = ['{ALIAS}', '{ID}'];
			$replace = [$user->seo_alias, $user->id];

			$url.= str_replace($search, $replace, $this->app->config->seo_user_url);
		}

		$this->app->plugins->run('uri_get_user', $url, $user, $this);

		return $url;
	}

	/**
	* Returns the url of a user's search by author page
	* @param int|array|object $user Either the users's data (array/object) or the user's ID. If data, these fields must be included: id,username,seo_alias
	* @return string The url
	*/
	public function getAuthor(int|array|object $user) : string
	{
		$user = $this->getUserData($user);
		if (!$user) {
			return $this->getEmpty();
		}

		return $this->getBlock($this->block_names['search'], '', ['user_id' => $user->id], [$user->seo_alias]);
	}

	/**
	* Returns the user's data
	* @param int|array|object $user Either the users's data (array/object) or the user's ID. If data, these fields must be included: id,username,seo_alias
	* @return object The user object
	*/
	protected function getUserData(int|array|object $user) : ?object
	{
		if (!$user) {
			return null;
		}

		if ($user instanceof \Venus\User\User) {
			$user = $user->getData(['id', 'seo_alias']);
		}

		if (is_array($user)) {
			$user = new Item($user);
		} elseif (!is_object($user)) {
			$user_id = (int)$user;
			if (!$user_id) {
				return null;
			}

			$user = $this->getPreloadedData('users', $user_id);
			if (!$user) {
				$user = $this->app->db->selectById('venus_users', $user_id, 'id, seo_alias');
			}
		}

		return $user;
	}

	/**
	* Returns the url of an admin block
	* @param string $name The name of the block. If left empty the current block is used
	* @param int $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @param string $controller The controller's name, if any
	* @return string The url
	*/
	public function getAdminBlock(string $name = '', string $action = '', array $params = [], string $controller = '') : string
	{
		if (!$name) {
			$name = $this->app->document->name;
		}

		$params = [$this->app->config->block_param => $name] + $params;
		if ($action) {
			$params[$this->app->config->action_param] = $action;
		}
		if ($controller) {
			$params[$this->app->config->controller_param] = $controller;
		}

		return $this->build($this->app->admin_index, $params);
	}

	/**
	* Returns the url from where a user can be edited in the admin
	* @param int $user_id The user's ID
	* @return string The url
	*/
	public function getAdminUser(int $user_id) : string
	{
		if (!$user_id) {
			return $this->getEmpty();
		}

		return $this->getAdminBlock('users', 'edit', ['id' => $user_id]);
	}

	/**
	* Returns the url of a dialog
	* @param string $name The name of the dialog
	* @return string The url
	*/
	public function getDialog(string $name) : string
	{
		return $this->app->url . VENUS_ASSETS_NAME . 'dialog.php?dialog_name=' . urlencode($name);
	}

	/**
	* Returns the url of an admin dialog
	* @param string $name The name of the dialog
	* @return string The url
	*/
	public function getAdminDialog(string $name) : string
	{
		return $this->app->admin_url . VENUS_ASSETS_NAME . 'dialog.php?dialog_name=' . urlencode($name);
	}

	/**
	* Appends the page param to $url.
	* @param string $url The url. If empty, $this->app->url is used
	* @param int $page The page number
	* @param string $page_param The name of the page param. If empty, $this->app->config->page_param is used
	* @return string The url
	*/
	public function appendPage(string $url = '', int $page = 0, string $page_param = '') : string
	{
		if (!$url) {
			$url = $this->app->url;
		}
		if (!$page_param) {
			$page_param = $this->app->config->page_param;
		}
		if (!$page) {
			$page = $this->app->request->getPage($page_param);
		}
		if (!$page) {
			return $url;
		}

		return $this->build($url, [$page_param => $page]);
	}

	/**
	* Returns preloaded data
	* @param string $type The type.
	* @param id $id The id
	* @return mixed The preloaded data, or null if it wasn't preloaded
	*/
	protected function getPreloadedData(string $type, int $id)// : ?object
	{
		if (isset($this->preloaded_data[$type][$id])) {
			return $this->preloaded_data[$type][$id];
		}

		return null;
	}

	/**
	* Preloads the pages data required to build page urls for the specified page ids
	* @param array $pids The page ids for which the data should be preloaded
	* @return $this
	*/
	public function preloadPages(array $pids)
	{
		if (!$pids) {
			return [];
		}

		$pages_data = $this->app->db->selectByIds('venus_pages', $pids, '', '', 'pid, category, seo_alias, seo_slug');

		$this->preloaded_data['pages'] = $this->preloaded_data['pages'] + $pages_data;

		return $this;
	}

	/**
	* Preloads the tags data required to build tag urls for the specified tag ids
	* @param array $tids The tag ids for which the data should be preloaded
	* @return $this
	*/
	public function preloadTags(array $tids)
	{
		if (!$tids) {
			return [];
		}

		$tags_data = $this->app->db->selectByIds('venus_tags', $tids, '', '', 'tid, seo_alias, seo_slug');

		$this->preloaded_data['tags'] = $this->preloaded_data['tags'] + $tags_data;

		return $this;
	}

	/**
	* Preloads the users data required to build user urls for the specified user ids
	* @param array $user_ids The user ids for which the data should be preloaded
	* @return $this
	*/
	public function preloadUsers(array $user_ids)
	{
		if (!$user_ids) {
			return [];
		}

		$users_data = $this->app->db->selectByIds('venus_users', $user_ids, '', '', 'id, seo_alias');

		$this->preloaded_data['users'] = $this->preloaded_data['users'] + $users_data;

		return $this;
	}

	/**
	* Loads data from the cache
	* @param string $type The type of the data. Eg: blocks/categories
	* @param string $key The cache key from where to read the data
	*/
	protected function loadCachedData(string $type, string $key)
	{
		if (isset($this->cached_data[$type])) {
			return;
		}

		$this->cached_data[$type] = $this->app->cache->get($key, true, []);
	}

	/**
	* Locates data in the cached_data array
	* @param string $type The type of the data. Eg: blocks/categories
	* @param string $id_name The name of the id field
	* @param int $id The id
	* @return array The data
	*/
	protected function findInCachedData(string $type, string $id_name, int $id)
	{
		$data = null;
		if (empty($this->cached_data[$type])) {
			return $data;
		}

		foreach ($this->cached_data[$type] as $c_data) {
			if ($c_data->$id_name == $id) {
				$data = $c_data;
				break;
			}
		}

		return $data;
	}

	/**
	* Returns data from the cached_data array
	* @param string $type The type of the data. Eg: blocks/categories
	* @param string $key The cache key from where to read the data
	* @return array
	*/
	protected function getCachedData(string $type, string $key)
	{
		if (!isset($this->cached_data[$type][$key])) {
			return null;
		}

		return $this->cached_data[$type][$key];
	}
}
