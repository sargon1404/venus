<?php
/**
* The Cache Class
* @package Venus
*/

namespace Venus;

/**
* The Cache Class
* Designed to cache css/javascript/modules/blocks content
*/
class Cache extends \Mars\Cache
{
	/**
	* @var string $table The database table used to store the cached data
	*/
	protected $table = 'venus_cache';

	/**
	* @var string $key The memcache key used to store the cached data, if any
	*/
	protected $key = 'venus_cache';

	/**
	* Builds a hash from the files
	* @param array $files The files to return the hash for
	* @return string The hash
	*/
	protected function getHash(array $files) : string
	{
		return hash('sha1', serialize($files));
	}

	/**
	* @see \Venus\Cache\Javascript\merge()
	*/
	protected function mergeJavascript(string $file, string $device, string $language, array $libraries, array $library_dependencies, array $local_urls)
	{
		$javascript = new \Venus\Cache\Javascript($this->app);
		$javascript->merge($file, $device, $language, $libraries, $library_dependencies, $local_urls);
	}

	/**
	* Returns the name under which the main javascript code will be cached
	* @param string $device The device
	* @param string $language The language's name
	* @return string
	*/
	public function getJavascriptFile(string $device, string $language = '') : string
	{
		return $this->getFile('main', 'js', $device, [$language]);
	}

	/**
	* Returns the url under which the main javascript code will be cached
	* @param string $device The device
	* @param string $language The language's name
	* @return string
	*/
	public function getJavascriptUrl(string $device, string $language = '') : string
	{
		return $this->cache_url . 'javascript/' . $this->getJavascriptFile($device, $language);
	}

	/**
	* Returns the name under which javascript files are merged
	* @param string $device The name of the device for which to build the merge
	* @param string $language The name of the language for which to build the merge
	* @param array $libraries The libraries to include in the merge
	* @param array $library_dependencies The javascript dependencies of non-javascript libraries
	* @param array $local_urls The local urls to include in the merge
	* @return string
	*/
	public function getJavascriptMergedFile(string $device, string $language, array $libraries, array $library_dependencies, array $local_urls) : string
	{
		static $javascript_merged = null;
		if ($javascript_merged === null) {
			$javascript_merged = $this->get('javascript_merged', true);
		}
		//var_dump($this->scope);die;
		$files = ['device' => $device, 'language' => $language, 'libraries' => $libraries, 'library_dependencies' => $library_dependencies, 'urls' => $local_urls];
		$hash = $this->getHash($files);
		$file = $hash . '.js';

		//check if we have the hash file cached
		if (!isset($javascript_merged[$hash])) {
			$this->mergeJavascript($file, $device, $language, $libraries, $library_dependencies, $local_urls);

			$javascript_merged[$hash] = true;

			$this->update('javascript_merged', $javascript_merged, true, null);
		}

		return $file;
	}

	/**
	* Returns the name under a theme's javascript code will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	/*public function getThemeJavascriptFile(string $name, string $device) : string
	{
		return $this->getFile('theme', 'js', $device, [$name]);
	}*/

	/**
	* Returns the url under a theme's javascript code will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	public function getThemeJavascriptUrl(string $name, string $device) : string
	{
		return $this->cache_url . 'javascript/' . $this->getThemeJavascriptFile($name, $device);
	}




	/**
	* Returns the url of a css url
	* @param string $name The name of the library
	* @return string
	*/
	public function getCssLibraryUrl(string $name) : string
	{
		return $this->base_cache_url . 'css/' . $this->getLibraryFile($name, 'css');
	}

	/**
	* Returns the url of of the file containing the js dependencies of a css library
	* @param string $name The name of the library
	* @return string
	*/
	public function getCssLibraryDependenciesUrl(string $name) : string
	{
		return $this->base_cache_url . 'javascript/' . $this->getLibraryDependencyFile($name, 'js');
	}

	/**
	* Returns the url of a javascript library
	* @param string $name The name of the library
	* @return string
	*/
	public function getJavascriptLibraryUrl(string $name) : string
	{
		return $this->base_cache_url . 'javascript/' . $this->getLibraryFile($name, 'js');
	}

	/**
	* Returns the url of of the file containing the css dependencies of a javascript library
	* @param string $name The name of the library
	* @return string
	*/
	public function getJavascriptLibraryDependenciesUrl(string $name) : string
	{
		return $this->base_cache_url . 'css/' . $this->getLibraryDependencyFile($name, 'css');
	}

	/**
	* Returns the cached categories
	* @return array The categories
	*/
	public function getCategories() : array
	{
		return $this->get('categories', true);
	}

	/**
	* Returns the cached category ids
	* @return array The ids
	*/
	public function getCategoryIds() : array
	{
		return $this->get('categories_ids', true);
	}

	/**
	* Returns the cached usergroups
	* @return array The usergroups
	*/
	public function getUsergroups() : array
	{
		return $this->get('usergroups', true);
	}

	/**
	* Returns the cached libraries
	* @return array The libraries
	*/
	public function getLibraries() : array
	{
		return $this->get('libraries', true);
	}

	/**
	* Clears the cached sitemap
	*/
	public function clearSitemap()
	{
		global $venus;
		$filename = VENUS_CACHE_DIR . 'sitemap.xml';
		$filename_gzip = $filename . '.gz';

		if (is_file($filename)) {
			$this->app->file->deleteFile($filename);
		}
		if (is_file($filename_gzip)) {
			$this->app->file->deleteFile($filename_gzip);
		}

		return $this;
	}

	/**
	* Clears cached rss feeds
	*/
	public function clearRss()
	{
		return $this->clearDir(VENUS_CACHE_RSS_DIR);
	}

	/**
	* Clears the templates cache
	*/
	public function clearTemplates()
	{
		global $venus;
		parent::clearTemplates();

		return $this->clearDir(VENUS_ADMIN_CACHE_TEMPLATES_DIR);
	}

	/**
	* Builds the users stats cache
	* @return $this
	*/
	public function buildUsers()
	{
		global $venus;
		$users_count = $this->app->db->count('venus_users');
		$users_disabled_count = $this->app->db->count('venus_users', ['status' => 0]);
		$users_deactivated_count = $this->app->db->count('venus_users', ['activated' => 0]);
		$users_banned_count = $this->app->db->count('venus_banned_users', ['status' => 1]);

		$this->update('users_count', $users_count);
		$this->update('users_disabled_count', $users_disabled_count);
		$this->update('users_deactivated_count', $users_deactivated_count);
		$this->update('users_banned_count', $users_banned_count);

		return $this;
	}

	/**
	* Builds the plugins cache
	* @return $this
	*/
	public function buildPlugins()
	{
		global $venus;
		$plugins_count = $this->app->db->count('venus_plugins');
		$plugins_blocks_count = $this->app->db->count('venus_plugins_blocks');
		$plugins_dialogs_count = $this->app->db->count('venus_plugins_dialogs');

		$this->update('plugins_count', $plugins_count);
		$this->update('plugins_blocks_count', $plugins_blocks_count);
		$this->update('plugins_dialogs_count', $plugins_dialogs_count);
		$this->update('plugins', '');
		$this->update('plugins_blocks_skip', '');
		$this->update('plugins_dialogs_skip', '');
		$this->update('plugins_admin_blocks_skip', '');
		$this->update('plugins_admin_dialogs_skip', '');

		return $this;
	}

	/**
	* Builds the banners cache
	* @return $this
	*/
	public function buildBanners()
	{
		global $venus;
		$banners_count = $this->app->db->count('venus_banners', ['status' => 1]);

		$this->update('banners_count', $banners_count);

		return $this;
	}

	/**
	* Clears the tags cache
	* @param array $tids the tag ids to clear
	*/
	public function clearTags($tids)
	{
		global $venus;
		if (!$tids) {
			return;
		}

		$in = $this->app->db->getIn($tids);
		$this->app->db->writeQuery("DELETE FROM venus_tags_cache WHERE tid IN({$in})");

		//delete from memcache, if enabled
		if ($this->app->config->memcache_enable) {
			$this->app->db->readQuery("SELECT ugid from venus_usergroups");
			$ugids = $this->app->db->getFields();
			$types = ['', 's' ,'t'];


			//delete the pages
			$this->app->db->readQuery("SELECT tid, memcache_pages_pages FROM venus_tags WHERE tid IN({$in})");
			$tags_data = $this->app->db->getList('tid', 'memcache_pages_pages', true);

			foreach ($tags_data as $tid => $memcache_pages_pages) {
				$pages = $memcache_pages_pages;
				if (!$pages) {
					$pages = $this->app->config->tag_memcache_pages_pages;
				}

				if (!$pages) {
					continue;
				}

				foreach ($ugids as $ugid) {
					foreach ($types as $type) {
						for ($page = 1; $page <= $pages; $page++) {
							$key = "tag-pages-{$tid}-{$ugid}-{$page}-{$type}";

							$this->app->memcache->delete($key);
						}
					}
				}
			}
		}

		return $this;
	}

	/**
	* Builds the widgets cache
	* @return $this
	*/
	public function buildWidgets()
	{
		global $venus;
		$widgets_count = $this->app->db->count('venus_widgets', 'WHERE status = 1');

		$this->update('widgets_count', $widgets_count);

		return $this;
	}

	/**
	* Clears the widgets cache
	* @return $this
	*/
	public function clearWidgets()
	{
		global $venus;
		$this->app->db->writeQuery("UPDATE venus_widgets SET languages = ''");

		return $this;
	}

	/**
	* Builds the menu cache
	* @return $this
	*/
	public function buildMenu()
	{
		global $venus;
		$menu_count = $this->app->db->count('venus_menu', 'WHERE status = 1');
		$menu_entries_count = $this->app->db->count('venus_menu_entries', 'WHERE status = 1');

		$this->app->db->readQuery("SELECT mid, name FROM venus_menu WHERE status = 1");
		$menu = $this->app->db->getList('name', 'mid');

		$this->app->db->readQuery("
		SELECT COUNT(*) as count, m.name FROM venus_menu_entries AS me
		LEFT JOIN venus_menu AS m ON m.mid = me.menu
		WHERE me.status = 1 and m.status = 1
		GROUP BY menu");
		$menu_data = $this->app->db->getList('name', 'count');


		$this->update('menu', serialize($menu));
		$this->update('menus', serialize($menu_data));
		$this->update('menu_output', '');
		$this->update('menu_count', $menu_count);
		$this->update('menu_entries_count', $menu_entries_count);

		return $this;
	}

	/**
	* Builds the announcements cache
	* @return $this
	*/
	public function buildAnnouncements()
	{
		global $venus;
		$announcements_count = $this->app->db->count('venus_announcements', 'WHERE status = 1');

		$this->update('announcements_count', $announcements_count);

		return $this;
	}

	/**
	* Builds the links cache
	* @return $this
	*/
	public function buildLinks()
	{
		global $venus;
		$links_count = $this->app->db->count('venus_links', 'WHERE status = 1');

		$this->update('links_count', $links_count);

		return $this;
	}

	/**
	* Builds the news cache
	* @return $this
	*/
	public function buildNews()
	{
		global $venus;
		$news_count = $this->app->db->count('venus_news', 'WHERE status = 1');

		$this->update('news_count', $news_count);

		return $this;
	}

	/**
	* Builds the comments cache
	* @return $this
	*/
	public function buildComments()
	{
		global $venus;
		$comments_count = $this->app->db->count('venus_comments', 'WHERE visible = 1');
		$comments_unpublished_count = $this->app->db->count('venus_comments', 'WHERE status = 0 AND visible = 1');
		$comments_spam_count = $this->app->db->count('venus_comments', 'WHERE is_spam = 1 AND visible = 1');

		$this->update('comments_count', $comments_count);
		$this->update('comments_unpublished_count', $comments_unpublished_count);
		$this->update('comments_spam_count', $comments_spam_count);

		return $this;
	}
}
