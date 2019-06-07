<?php
/**
* The Admin Cache Class
* @package Venus
*/

namespace Venus\Admin;

use Venus\Theme;
use Venus\Themes;

/**
* The Admin Cache Class
* Designed to cache the css/javascript/modules/blocks content
*/
class Cache extends \Venus\Cache
{
	/**
	* @internal
	*/
	protected $scope = 'admin';

	/**
	* Builds the cache object
	*/
	/*public function __construct(App $app)
	{
		parent::__construct($app);

		$this->base_cache_url = $this->app->cache_url;
		$this->cache_url = $this->app->admin_cache_url;
		App::dd($this);die;
	}*/

	/**
	* Clears a folder and copies the empty index.htm file
	* @param string $dir The folder's name
	*/
	protected function clearDir($dir)
	{
		$this->app->file->cleanDir($dir);

		$this->app->file->copyFile($this->app->site_dir . 'src/index.htm', $dir . 'index.htm');
	}

	/**
	* Returns the url under which the main javascript code will be cached, in the frontend
	* @param string $device The device
	* @param string $language The language's name
	* @return string
	*/
	/*public function getFrontendJavascriptUrl(string $device, string $language = '') : string
	{
		return $this->app->cache_url . 'javascript/' . $this->getJavascriptFile($device, $language);
	}*/

	/**
	* @see \Venus\Cache\Javascript\merge()
	*/
	/*protected function mergeJavascript(string $file, string $device, string $language, array $libraries, array $library_dependencies, array $local_urls)
	{
		$javascript = new \venus\admin\cache\Javascript($this->app);
		$javascript->merge($file, $device, $language, $libraries, $library_dependencies, $local_urls);
	}*/



	/**
	* Builds the css cache
	* @return $this
	*/
	public function buildCss()
	{
		//$this->buildCssFrontend();
		$this->buildCssAdmin();
die("jjjj");
		return $this;
	}

	/**
	* Builds the css frontend cache
	* @return $this
	*/
	public function buildCssFrontend()
	{
		$this->clearDir($this->app->cache_dir . 'css/');

		$css = new \Venus\Assets\Css($this->app);
		$css->buildCache();

		$this->cacheThemeDefault();

		//clear the list of merged files
		$this->update('css_merged', '', false, 'frontend');

		$this->update('css_dateline', time());

		return $this;
	}

	/**
	* Builds the css admin cache
	* @return $this
	*/
	public function buildCssAdmin()
	{
		$this->clearDir($this->app->admin_cache_dir . 'css/');

		$css = new \Venus\Admin\Assets\Css($this->app);
		$css->buildCache();

		//clear the list of merged files
		$this->update('css_merged', '', false, 'admin');

		return $this;
	}

	/**
	* Builds the javascript cache
	* @return $this
	*/
	public function buildJavascript()
	{
		$this->buildJavascriptFrontend();
		$this->buildJavascriptAdmin();
	}

	/**
	* Builds the javascript frontend cache
	*/
	public function buildJavascriptFrontend()
	{
		$this->clearDir($this->app->cache_dir . 'javascript/');

		$javascript = new \Venus\Assets\Javascript($this->app);
		$javascript->buildCache();

		$this->cacheThemeDefault();

		//clear the list of merged files
		$this->update('javascript_merged', '', false, 'frontend');

		$this->update('javascript_dateline', time());

		return $this;
	}

	/**
	* Builds the javascript admin cache
	*/
	public function buildJavascriptAdmin()
	{
		$this->clearDir($this->app->admin_cache_dir . 'javascript/');

		$javascript = new \Venus\Admin\Assets\Javascript($this->app);
		$javascript->buildCache();

		//clear the list of merged files
		$this->update('javascript_merged', '', false, 'admin');

		return $this;
	}










	/**
	* Builds the libraries cache
	* @return $this
	*/
	public function buildLibraries()
	{
		$libraries = ['css' => [], 'javascript' => []];

		$this->app->file->listDir($this->app->libraries_dir . 'css', $css_dirs, $files);
		foreach ($css_dirs as $name) {
			$data = $this->readLibraryData('css', $name);
			$this->buildCssLibrary($name, $data);

			//does this js library have css files?
			$libraries['css'][$name] = $this->getLibraryData($data);
		}

		$this->app->file->listDir($this->app->libraries_dir . 'javascript', $js_dirs, $files);
		foreach ($js_dirs as $name) {
			$data = $this->readLibraryData('javascript', $name);
			$this->buildJavascriptLibrary($name, $data);

			//does this js library have css files?
			$libraries['javascript'][$name] = $this->getLibraryData($data);
		}

		$this->update('libraries', $libraries, true);

		//clear the list of merged files
		$this->update('css_merged', '', false, 'frontend');
		$this->update('css_merged', '', false, 'admin');

		$this->update('javascript_merged', '', false, 'frontend');
		$this->update('javascript_merged', '', false, 'admin');

		return $this;
	}

	/**
	* Reads the data of a library
	* @param string $dir The dir where the library is located, relative to the libraries folder
	* @param string $name The name of the library
	* @return array The data
	*/
	protected function readLibraryData(string $dir, string $name) : array
	{
		return include($this->app->libraries_dir . App::sl($dir) . App::sl($name) . 'data.php');
	}

	/**
	* Returns the data of a library
	* @param array $data The read data
	* @return array The data
	*/
	protected function getLibraryData(array $data) : array
	{
		$library_data = [
			'location' => $data['location'] ?? 'head',
			'priority' => $data['priority'] ?? 10000,
			'async' => $data['async'] ?? false,
			'defer' => $data['defer'] ?? false,
			'dependencies' => [],
		];

		if (!empty($data['dependencies'])) {
			$dependencies_data = [
				'location' => $data['dependencies']['location'] ?? 'head',
				'priority' => $data['dependencies']['priority'] ?? 10000,
				'async' => $data['dependencies']['async'] ?? false,
				'defer' => $data['dependencies']['defer'] ?? false,
			];

			$library_data['dependencies'] = $dependencies_data;
		}

		return $library_data;
	}

	/**
	* Builds the cache of a css library
	* @param string $name The name of the library
	* @param array $data The library's data. If null, will be read from the disk
	* @return $this
	*/
	public function buildCssLibrary(string $name, ?array $data = null)
	{
		if ($data === null) {
			//read the library's data
			$data = $this->readLibraryData('css', $name);
		}

		$css_files = $data['files'] ?? [];
		$js_files = $data['dependencies']['files'] ?? [];

		$css = new \Venus\Assets\Css($this->app);
		$css->cacheLibrary($name, $css_files, $js_files);

		return $this;
	}

	/**
	* Builds the cache of a javascript library
	* @param string $name The name of the library
	* @param array $data The library's data. If null, will be read from the disk
	* @return $this
	*/
	public function buildJavascriptLibrary(string $name, ?array $data = null)
	{
		if ($data === null) {
			//read the library's data
			$data = $this->readLibraryData('javascript', $name);
		}

		$js_files = $data['files'] ?? [];
		$css_files = $data['dependencies']['files'] ?? [];

		$javascript = new \Venus\Assets\Javascript($this->app);
		$javascript->cacheLibrary($name, $js_files, $css_files);

		return $this;
	}











	/**
	* Builds the themes cache
	* @return $this
	*/
	public function themes()
	{
		$themes = new Themes;
		$themes->load();

		foreach ($themes as $theme) {
			$templates = App::serialize($theme->getTemplates());

			$this->app->db->updateById('venus_themes', ['templates' => $templates], 'tid', $theme->tid);
		}

		$this->cacheThemeDefault();

		return $this;
	}

	/**
	* Caches the data of the default theme
	*/
	public function cacheThemeDefault()
	{
		$theme = new Theme;
		$this->update('theme_default', $theme->getRow($this->app->config->theme_default), true);
	}

	/**
	* Builds the languages cache for all languages
	* @return $this
	*/
	public function languages()
	{
		$languages = new Languages;
		$languages->load();

		foreach ($languages as $language) {
			$files = App::serialize($language->getFiles());

			$this->app->db->updateById('venus_languages', ['files' => $files], 'lid', $language->lid);
		}

		$this->cacheLanguageDefault();

		return $this;
	}

	/**
	* Caches the data of the default language
	*/
	public function cacheLanguageDefault()
	{
		$language = new Language;
		$this->update('language_default', $language->getRow($this->app->config->language_default), true);
	}

	/**
	* Builds the usergroups data cache
	* @return $this
	*/
	public function usergroups()
	{
		$permissions = [];
		$permissions_array = $this->app->db->select('venus_usergroups_permissions');
		$permissions_list = ['view', 'comment', 'rate', 'add', 'publish', 'publish_own', 'edit', 'edit_own', 'delete', 'delete_own'];

		foreach ($permissions_array as $perm) {
			$perm_array = [];
			foreach ($permissions_list as $pl) {
				$perm_array[$pl] = $perm->$pl;
			}

			$permissions[$perm->type][$perm->ugid] = $perm_array;
		}

		$guests = $this->app->db->selectRow('venus_usergroups', '*', ['ugid' => APP::USERGROUPS['guests']]);
		$usergroups = $this->app->db->selectWithKey('venus_usergroups', 'ugid');

		$this->update('usergroups', $usergroups, true);
		$this->update('usergroup_guests', $guests, true);
		$this->update('usergroups_permissions', $permissions, true);
		$this->update('usergroups_timestamp', time());

		return $this;
	}

	/**
	* Builds the blocks cache
	* @return $this
	*/
	public function blocks()
	{
		$this->app->db->readQuery('SELECT bid, status, category, name, seo_alias, seo_slug FROM venus_blocks');
		$blocks = $this->app->db->get('name');

		$this->update('blocks', $blocks, true);

		return $this;
	}

	/**
	* Clears the blocks cache
	* @return $this
	*/
	public function clearBlocks()
	{
		$this->app->db->writeQuery("UPDATE venus_blocks SET languages = ''");
		$this->app->db->writeQuery("UPDATE venus_admin_blocks SET languages = ''");

		return $this;
	}

	/**
	* Builds the user notifications cache
	* @return $this
	*/
	public function userNotifications()
	{
		$notifications_count = $this->app->db->count('venus_users_notifications');

		$this->update('users_notifications', $notifications_count);

		return $this;
	}

	/**
	* Builds the snippets cache
	* @return $this
	*/
	public function buildSnippets()
	{
		global $venus;
		$snippets_count = $this->app->db->count('venus_snippets', ['status' => 1]);

		$this->update('snippets_count', $snippets_count);

		return $this;
	}

	/**
	* Builds the categories cache
	* @return $this
	*/
	public function buildCategories()
	{
		global $venus;
		$this->app->db->readQuery('SELECT * FROM venus_categories');
		$categories = $this->app->db->get('cid');

		//Build the seo slugs for the category's parents
		foreach ($categories as $category) {
			$parents = $this->getCategoryParents($categories, $category);

			$category->parents_slug = implode('/', $parents);
		}

		$this->update('categories', $categories, true);
		$this->update('categories_ids', array_keys($categories), true);

		$this->clearCategoriesCount();

		return $this;
	}

	/**
	* Returns the parents of a category
	* @return string
	*/
	protected function getCategoryParents($categories, $category)
	{
		$parents = [];

		$lineage = explode('-', $category->lineage);
		if (count($lineage) <= 1) {
			return $parents;
		}

		foreach ($lineage as $parent_id) {
			if ($parent_id == $category->cid) {
				continue;
			}

			$parents[] = $categories[$parent_id]->seo_alias;
		}

		return $parents;
	}

	/**
	* Resets the counts (subcategories, news, links, blocks) of all categories, indicating it must be recounted
	* @return $this
	*/
	public function clearCategoriesCount()
	{
		global $venus;
		$this->app->db->writeQuery('UPDATE venus_categories SET cached_subcategories_count = -1, cached_blocks_count = -1, cached_news_count = -1, cached_links_count = -1');

		return $this;
	}

	/**
	* Clears the category cache
	* @param array $cids The category ids to clear the cache for. If nothing is specified, all categories are cleared
	* @return $this
	*/
	public function clearCategories(array $cids = [])
	{
		global $venus;
		if ($cids) {
			$this->app->db->deleteByIds('venus_categories_cache', 'cid', $cids);

			//delete from memcache, if enabled
			if ($this->app->config->memcache_enable) {
				var_dump("aici");
				die;
				$this->app->db->readQuery("SELECT ugid from venus_usergroups");
				$ugids = $this->app->db->getFields();
				$types = ['', 's' ,'t'];

				//delete the news
				foreach ($cids as $cid) {
					foreach ($ugids as $ugid) {
						$key = "cat-news-{$cid}-{$ugid}";
						$this->app->memcache->delete($key);
					}
				}

				//delete the pages
				$cat_data = $this->app->db->selectByIds('venus_categories', 'cid', $cids, '', '', 'cid, memcache_pages_pages');

				foreach ($cat_data as $cid => $memcache_pages_pages) {
					$pages = $memcache_pages_pages;
					if (!$pages) {
						$pages = $this->app->config->category_memcache_pages_pages;
					}

					if (!$pages) {
						continue;
					}

					foreach ($ugids as $ugid) {
						foreach ($types as $type) {
							for ($page = 1; $page <= $pages; $page++) {
								$key = "cat-pages-{$cid}-{$ugid}-{$page}-{$type}";

								$this->app->memcache->delete($key);
							}
						}
					}
				}
			}
		} else {
			$this->app->db->writeQuery("TRUNCATE venus_categories_cache");
		}

		return $this;
	}
}