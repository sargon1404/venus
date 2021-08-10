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
	protected string $table = 'venus_cache';

	/**
	* @var string $key The memcache key used to store the cached data, if any
	*/
	protected string $memcache_key = 'venus-cache';

	/**
	* Builds the javascript cache from the /javascript folder
	* @return $this
	*/
	public function buildMainJavascript()
	{
		$javascript = new \Venus\Assets\Javascript($this->app);

		$javascript->cacheMain();
		$javascript->cacheInline();
	}

	/**
	* Builds the css & javascript cache for a theme
	* @param Theme $theme The theme to build the css cache for
	*/
	public function buildForTheme(Theme $theme)
	{
		$css = new \Venus\Assets\Css($this->app);
		$css->cacheTheme($theme);

		$javascript = new \Venus\Assets\Javascript($this->app);
		$javascript->cacheTheme($theme);

		$this->app->plugins->run('cache_build_for_theme', $theme, $css, $javascript, $this);
	}

	/**
	* Returns the cached usergroups
	* @return array The usergroups
	*/
	public function getUsergroups() : array
	{
		return $this->get('usergroups');
	}

	/**
	* Returns the cached menus
	* @return array The menus
	*/
	public function getMenus() : array
	{
		return $this->get('menus');
	}

	/**
	* Returns the cached libraries
	* @return array The libraries
	*/
	public function getLibraries() : array
	{
		return $this->get('libraries');
	}


	/**
	* Returns the cached categories
	* @return array The categories
	*/
	public function getCategories() : array
	{
		return $this->get('categories');
	}

	/**
	* Returns the cached category ids
	* @return array The ids
	*/
	public function getCategoryIds() : array
	{
		return $this->get('categories_ids');
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
			$this->app->file->delete($filename);
		}
		if (is_file($filename_gzip)) {
			$this->app->file->delete($filename_gzip);
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

		$this->update('users_count', $users_count, 'frontend');
		$this->update('users_disabled_count', $users_disabled_count, 'frontend');
		$this->update('users_deactivated_count', $users_deactivated_count, 'frontend');
		$this->update('users_banned_count', $users_banned_count, 'frontend');

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

		$this->update('banners_count', $banners_count, 'frontend');

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

		$this->update('widgets_count', $widgets_count, 'frontend');

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
		var_dump("cache-build-menu");
		die;
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

		$this->update('announcements_count', $announcements_count, 'frontend');

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

		$this->update('links_count', $links_count, 'frontend');

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

		$this->update('news_count', $news_count, 'frontend');

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

		$this->update('comments_count', $comments_count, 'frontend');
		$this->update('comments_unpublished_count', $comments_unpublished_count, 'frontend');
		$this->update('comments_spam_count', $comments_spam_count, 'frontend');

		return $this;
	}
}
