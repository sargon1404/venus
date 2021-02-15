<?php
/**
* The Block Class
* @package Venus
*/

namespace Venus;

/**
* The Block Class
* Object corresponding to a block extension
*/
class Block extends \Venus\Extensions\Extension implements Document
{
	use DocumentBody;

	use \Venus\Extensions\LoadTrait;
	use \Venus\Extensions\LanguagesTrait;
	use \Venus\Extensions\TemplatesTrait;
	use \Venus\Extensions\PluginsTrait;
	use \Venus\Extensions\MvcTrait;

	/**
	* @var int $id The block's id
	*/
	public int $id = 0;

	/**
	* @var int $id1 The block's sub item 1
	*/
	public int $id1 = 0;

	/**
	* @var int $id2 The block's sub item 2
	*/
	public int $id2 = 0;

	/**
	* @var int $id3 The block's sub item3
	*/
	public int $id3 = 0;

	/**
	* @internal
	*/
	protected static string $table = 'venus_blocks';

	/**
	* Needed by ConfigTrait
	* @internal
	*/
	protected static string $config_prefix = 'block_';

	/**
	* Needed by ImageTrait
	* @internal
	*/
	protected static string $image_type = 'image';

	/**
	* Needed by ImageTrait
	* @internal
	*/
	protected static bool $image_subdir = false;

	/**
	* Needed by CategoryTrait
	* @internal
	*/
	protected static string $category_image_type = 'small_thumb';

	/**
	* @internal
	*/
	protected static bool $languages_cache = true;

	/**
	* @internal
	*/
	protected static string $type = 'block';

	/**
	* @internal
	*/
	protected static string $base_dir = 'blocks';

	/**
	* @internal
	*/
	protected static string $namespace = "\\Cms\\Extensions\\Blocks\\";

	/**
	* Builds a block extension object
	* @param int|string $name The name of the block or the block id.
	*/
	public function __construct(int|string $name)
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
		$this->validator = $this->app->validator;

		$block = $this->get($name);
		if (!$block) {
			return;
		}

		$this->load($block);
	}

	/**
	* Returns the data of a block
	* @param int|string $name The name of the block *or* the block id
	* @return object The block
	*/
	protected function get(int|string $name)
	{
		$table = $this->getTable();

		$where_sql = '';
		if (is_int($name)) {
			$where_sql = 'b.id = :block_name';
		} else {
			$where_sql = 'b.name_crc = CRC32(:block_name) AND b.name = :block_name';
		}

		$sql = "
			SELECT *
			FROM {$table} AS b
			WHERE {$where_sql}";

		$this->app->db->readQuery($sql, ['block_name' => $name]);
		$block = $this->app->db->getRow();

		return $block;
	}

	/**
	* @see \Venus\Extensions\Extension::prepare()
	* {@inheritdoc}
	*/
	protected function prepare()
	{
		parent::prepare();

		$this->preparePermissions();

		$this->prepareConfig();
		$this->prepareTitle();
		$this->prepareSeo();
		$this->prepareMeta();
		$this->prepareImage();
		$this->prepareCategory();
		$this->prepareCategories();
		$this->prepareHas(['description']);
		$this->prepareModerator();
		$this->prepareSystem();

		$this->app->plugins->run('block_prepare', $this);
	}

	/**
	* @see \Venus\Extensions\Extension::preparePaths()
	* {@inheritdoc}
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		$this->url = $this->getUrl();
	}

	/**
	* Returns the block's url
	* @return string
	*/
	protected function getUrl() : string
	{
		return $this->app->uri->getBlock($this->name);
	}

	/**
	* Prepares the config options of the block
	*/
	protected function prepareConfig()
	{
		$config_array1 = [
			'layout', 'show_widgets', 'show_breadcrumbs', 'show_title', 'show_description',
			'show_image', 'show_category', 'show_tags', 'show_rating', 'show_comments',
			'seo_rel', 'seo_target', 'meta_robots', 'cache', 'cache_comments', 'track_hits',
			'comments_open', 'comments_show_count', 'ratings_open', 'ratings_show_count'
		];
		$config_array2 = ['meta_author'];
		$config_array3 = ['cache_interval', 'cache_comments_interval', 'comments_per_page'];
		$config_categories_array = ['seo_rel', 'seo_target'];

		$this->setConfig($config_array1);
		$this->setConfig($config_array2, '');
		$this->setConfig($config_array3, 0);

		//override the comments per page option, if a global $this->app->config->comments_per_page is set
		if ($this->app->config->comments_per_page) {
			$this->comments_per_page = $this->app->config->comments_per_page;
		}
	}

	/**
	* Prepares the system categories etc..
	*/
	protected function prepareSystem()
	{
		$this->app->categories = $this->categories;
	}

	/**
	* Return's the block's content
	* @param string $action The action to be performed. If empty $this->app->request->get_action is used
	* @return string The content
	*/
	public function getContent(string $action = '') : string
	{
		if ($this->isCached() && !$this->debug) {
			//can the content be read from the cache?
			return $this->cached;
		} else {
			$this->controller_action = $action;

			//$content = $this->getDocumentContent($action);
			$content = $this->run();

			//cache the content,if cachable
			$this->buildCache();

			return $content;
		}
	}
}
