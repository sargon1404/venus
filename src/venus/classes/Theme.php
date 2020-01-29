<?php
/**
* The Theme Class
* @package Venus
*/

namespace Venus;

/**
* The Theme Class
* Object corresponding to a theme extension
*/
class Theme extends \Venus\Extensions\Extension
{
	use \Mars\Theme {
		preparePaths as protected prepareBasePaths;
	}

	/**
	* @var string $site_index Alias for $this->app->site_index
	*/
	public string $site_index = '';

	/**
	* @var int $tid The theme's id
	*/
	public int $tid = 0;

	/**
	* @var int $parent The id of the parent theme, if any
	*/
	public int $parent = 0;

	/**
	* @var string $parent_name The name of the theme's parent theme, if any
	*/
	public string $parent_name = '';

	/**
	* @var string $parent_templates_dir The filesystem path for the theme's parent templates folder
	*/
	public string $parent_templates_dir = '';

	/**
	* @var array $templates Array with the keys listing the available templates of the theme
	*/
	public array $templates = [];

	/**
	* @var array $parent_templates Array with the keys listing the available templates of the parent theme
	*/
	public array $parent_templates = [];

	/**
	* @var string $root_images_dir The filesystem path for the theme's regular images folder. Unlike images_dir which might point to the tables/smartphones images dir, it will always point to the main/base images dir
	*/
	public string $root_images_dir = '';

	/**
	* @var string $root_images_url The url of the theme's images folder. Unlike images_url which might point to the tables/smartphones images dir, it will always point to the main/base images dir
	*/
	public string $root_images_url = '';

	/**
	* @var bool $has_javascript_dir True if the theme has a javascript dir
	*/
	public bool $has_javascript_dir = false;

	/**
	* @var bool $has_images_dir True if the theme has an image folder
	*/
	public bool $has_images_dir = false;

	/**
	* @var bool $has_mobile_images_dir True if the mobile version has an image folder
	*/
	public bool $has_mobile_images_dir = false;

	/**
	* @var bool $has_tablets_images_dir True if the tablets version has an image folder
	*/
	public bool $has_tablets_images_dir = false;

	/**
	* @var bool $has_smartphones_images_dir True if the smartphones version has an image folder
	*/
	public bool $has_smartphones_images_dir = false;

	/**
	* @var bool $parent_has_javascript_dir True if the parent theme has a javascript dir
	*/
	public array $parent_has_javascript_dir = [];

	/**
	* @var bool $parent_has_images_dir True if the parent theme has an image folder
	*/
	public bool $parent_has_images_dir = false;

	/**
	* @var bool $parent_has_mobile_images_dir True if the parent theme has a mobile image folder
	*/
	public bool $parent_has_mobile_images_dir = false;

	/**
	* @var bool $parent_has_tablets_images_dir True if the parent theme has a tablets image folder
	*/
	public bool $parent_has_tablets_images_dir = false;

	/**
	* @var bool $parent_has_smartphones_images_dir True if the parent theme has a smartphones image folder
	*/
	public bool $parent_has_smartphones_images_dir = false;

	/**
	* @var array $base_params The theme's base params
	*/
	public array $base_params = [
		'create_tablets_images' => false,
		'create_smartphones_images' => false,

		'site_logo' => '',
		'site_slogan' => '',

		'viewport_output' => false,
		'viewport_width' => 'device-width',
		'viewport_initial_scale' => '1.0',

		'menu_image_width' => 40,
		'menu_image_height' => 0,

		'avatar_image_width' => 100,
		'avatar_image_height' => 0,
		'avatar_thumb_width' => 50,
		'avatar_thumb_height' => 0,
		'avatar_small_thumb_width' => 24,
		'avatar_small_thumb_height' => 0,

		'category_image_width' => 300,
		'category_image_height' => 0,
		'category_thumb_width' => 120,
		'category_thumb_height' => 120,
		'category_small_thumb_width' => 0,
		'category_small_thumb_height' => 24,

		'block_image_width' => 400,
		'block_image_height' => 0,
		'block_thumb_width' => 120,
		'block_thumb_height' => 120,
		'block_small_thumb_width' => 120,
		'block_small_thumb_height' => 0,

		'page_image_width' => 300,
		'page_image_height' => 0,
		'page_thumb_width' => 150,
		'page_thumb_height' => 0,
		'page_small_thumb_width' => 120,
		'page_small_thumb_height' => 0,

		'announcement_image_width' => 240,
		'announcement_image_height' => 0,
		'announcement_thumb_width' => 120,
		'announcement_thumb_height' => 0,

		'news_image_width' => 600,
		'news_image_height' => 0,
		'news_thumb_width' => 250,
		'news_thumb_height' => 0,
		'news_small_thumb_width' => 120,
		'news_small_thumb_height' => 0,

		'tag_image_width' => 120,
		'tag_image_height' => 0,
		'tag_thumb_width' => 64,
		'tag_thumb_height' => 0,
	];

	/**
	* @var array $base_params_mobile The theme's base mobile params
	*/
	public array $base_params_mobile = [
		'mobile' => [],
		'tablet' => [],
		'smartphone' => []
	];

	/**
	* @var object $params The theme's params
	*/
	public $params = null;

	/**
	* @var array $parent_params The theme's parent params
	*/
	public array $parent_params = [];

	/**
	* @var array $params_data Array where the unserialized params data is stored
	*/
	protected array $params_data = [];

	/**
	* @var array $parent_params_data Array where the unserialized params data is stored
	*/
	protected array $parent_params_data = [];

	/**
	* @internal
	*/
	protected static string $id_name = 'tid';

	/**
	* @internal
	*/
	protected static string $table = 'venus_themes';

	/**
	* Builds the current theme object
	* @param mixed $theme The theme's id/data
	*/
	public function __construct($theme = 0)
	{
		parent::__construct($theme);

		$this->app->plugins->run('themeConstruct', $this, $theme);
	}

	/**
	* Returns a row from the theme's database table
	* @param int $tid The theme's id
	* @return object The theme's data
	*/
	public function getRow(int $tid) : object
	{
		$table = $this->getTable();

		$this->app->db->readQuery("
			SELECT
			t.*, p.name as parent_name, p.templates as parent_templates,
			p.has_javascript_dir as parent_has_javascript_dir,
			p.has_images_dir as parent_has_images_dir, p.has_mobile_images_dir as parent_has_mobile_images_dir,
			p.has_tablets_images_dir as parent_has_tablets_images_dir, p.has_smartphones_images_dir as parent_has_smartphones_images_dir,
			p.params as parent_params
			FROM {$table} as t
			LEFT JOIN {$table} as p ON t.parent = p.tid
			WHERE t.tid = {$tid} AND t.status = 1");

		return $this->app->db->getRow();
	}

	/**
	* @see \Venus\Extensions\Extension::prepare()
	* {@inheritDoc}
	*/
	protected function prepare()
	{
		parent::prepare();

		$this->prepareTemplates();
	}

	/**
	* @see \Venus\Extensions\Extension::preparePaths()
	* {@inheritDoc}
	*/
	protected function preparePaths()
	{
		$this->prepareBasePaths();
		$this->prepareImagePaths();
	}

	/**
	* Prepares the image paths
	*/
	protected function prepareImagePaths()
	{
		$this->root_images_dir = $this->images_dir;
		$this->root_images_url = $this->images_url;

		[$this->images_dir, $this->images_url] = $this->getImagePaths($this->app->device->type);
	}

	/**
	* Returns the image paths of a device
	* @param string $device the device type
	* @return array The images path & url
	*/
	public function getImagePaths(string $device = '') : array
	{
		$images_dir = '';
		$images_url = '';
		$parent_images_dir = '';
		$parent_images_url = '';

		if ($this->parent) {
			$parent_images_dir = $this->getDir($this->parent_name) . App::EXTENSIONS_DIRS['images'];
			$parent_images_url = $this->getDirUrlStatic($this->parent_name) . App::EXTENSIONS_DIRS['images'];
		}

		if ($device != 'desktop') {
			$device_dir = $this->app->device->getSubdir($device);

			if ($device == 'tablet') {
				if ($this->has_tablets_images_dir) {
					$images_dir = $this->images_dir . $device_dir;
					$images_url = $this->images_url . $device_dir;
				} elseif ($this->parent && $this->parent_has_tablets_images_dir) {
					$images_dir = $parent_images_dir . $device_dir;
					$images_url = $parent_images_url . $device_dir;
				}
			} elseif ($device == 'smartphone') {
				if ($this->has_smartphones_images_dir) {
					$images_dir = $theme->images_dir . $device_dir;
					$images_url = $theme->images_url . $device_dir;
				} elseif ($this->parent && $this->parent_has_smartphones_images_dir) {
					$images_dir = $parent_images_dir . $device_dir;
					$images_url = $parent_images_url . $device_dir;
				}
			}

			if (!$images_dir) {
				//load the images from the mobile folder
				$device_dir = $this->app->device->getSubdir('mobile');

				if ($this->has_mobile_images_dir) {
					$images_dir = $this->images_dir . $device_dir;
					$images_url = $this->images_url . $device_dir;
				} elseif ($this->parent && $this->parent_has_mobile_images_dir) {
					$images_dir = $parent_images_dir . $device_dir;
					$images_url = $parent_images_url . $device_dir;
				}
			}
		}

		if (!$images_dir) {
			if ($this->has_images_dir) {
				$images_dir = $this->images_dir;
				$images_url = $this->images_url;
			} elseif ($this->parent && $theme->parent_has_images_dir) {
				$images_dir = $parent_images_dir;
				$images_url = $parent_images_url;
			}
		}

		if (!$images_dir && !$images_url) {
			throw new \Exception("Theme {$theme->name} must have an images folder");
		}

		return [$images_dir, $images_url];
	}

	/**
	* Prepares the theme's params
	*/
	protected function prepareParams()
	{
		$this->params_data = App::unserialize($this->params);
		$this->parent_params_data = App::unserialize($this->parent_params);

		$this->params = App::toObject($this->getParams($this->app->device->type));

		unset($this->parent_params);
	}

	/**
	* Returns the params of the theme. Merges the base params, with the parent params and the theme's params, based on device
	* @param string $device the device type
	* @return array The params
	*/
	public function getParams(string $device = 'desktop') : array
	{
		$params = [];
		$parent_params = [];

		if ($this->parent) {
			$parent_params = $this->parent_params_data['desktop'];

			if ($device != 'desktop') {
				$parent_params = array_merge($parent_params, $this->parent_params_data['mobile'], $this->parent_params_data[$device]);
			}
		}

		$base_params = $this->base_params;
		$params = $this->params_data['desktop'] ;

		if ($device != 'desktop') {
			$base_params = array_merge($base_params, $this->base_params_mobile['mobile'], $this->base_params_mobile[$device]);
			$params = array_merge($params, $this->params_data['mobile'], $this->params_data[$device]);
		}

		return array_merge($base_params, $parent_params, $params);
	}

	/**
	* Prepares the template list this theme has
	*/
	protected function prepareTemplates()
	{
		if ($this->parent) {
			$this->parent_templates_dir = $this->getDir($this->parent_name) . App::EXTENSIONS_DIRS['templates'];
		}

		$this->templates = App::unserialize($this->templates);
		$this->parent_templates = App::unserialize($this->parent_templates);
	}

	/*****************IMAGES DIMENSIONS METHODS*********************************/

	/**
	* Returns the width of an image, based on the loaded theme's device
	* @param string $prefix The image's prefix. Eg: category/menu/page etc..
	* @param string $type The image's type Eg: image/thumb/small_thumb
	* @return int
	*/
	public function getImageWidth(string $prefix, string $type = 'image') : int
	{
		if (!$type) {
			$type = 'image';
		}

		$key = $prefix . '_' . $type . '_width';

		return (int)$this->params->$key;
	}

	/**
	* Returns the height of an image, based on the theme's device
	* @param string $prefix The image's prefix. Eg: category/menu/page etc..
	* @param string $type The image's type Eg: image/thumb/small_thumb
	* @return int
	*/
	public function getImageHeight(string $prefix, string $type = 'image') : int
	{
		if (!$type) {
			$type = 'image';
		}

		$key = $prefix . '_' . $type . '_height';

		return (int)$this->param->$key;
	}

	/**************TEMPLATES METHODS**************************/

	/**
	* Determines if a template exists
	* @param string $name The name of the template
	* @return bool True if the template exists
	*/
	public function hasTemplate(string $name) : bool
	{
		if ($this->templateExists($name)) {
			return true;
		} elseif ($this->parentTemplateExists($name)) {
			return true;
		}

		return false;
	}

	/**
	* Checks if a template exists
	* @param string $filename The template's filename *relative* to the theme's templates folder
	* @return bool
	*/
	public function templateExists(string $filename) : bool
	{
		$filename.= '.' . App::FILE_EXTENSIONS['templates'];

		if ($this->development) {
			return is_file($this->templates_dir . $filename);
		}

		if (isset($this->templates[$filename])) {
			return true;
		}

		return false;
	}

	/**
	* Checks if a template exists in the theme's parent folder
	* @param string $filename The template's filename *relative* to the theme's templates folder
	* @return bool
	*/
	public function parentTemplateExists(string $filename) : bool
	{
		$filename.= '.' . App::FILE_EXTENSIONS['templates'];

		if ($this->development) {
			return is_file($this->parent_templates_dir . $filename);
		}

		if (isset($this->parent_templates[$filename])) {
			return true;
		}

		return false;
	}

	/**
	* Returns the templates of a theme
	* @return array The templates
	*/
	public function getTemplates() : array
	{
		$templates = [];
		$this->app->file->listDir($this->templates_dir, $dirs, $templates, false, true);

		if ($templates) {
			$templates = array_fill_keys($templates, true);
		}

		return $templates;
	}

	/**
	* Updates the theme's javascript inline code
	* @param array $code The inline code
	*/
	public function updateInlineJs(array $code = [])
	{
		$code = $this->app->filter->trim($code);

		$this->app->db->updateById($this->getTable(), ['inline_js' => App::serialize($code)], $this->getIdName(), $this->getId());
	}

	/**
	* Updates the theme's css inline code
	* @param array $code The inline code
	*/
	public function updateInlineCss(array $code = [])
	{
		$code = $this->app->filter->trim($code);

		$this->app->db->updateById($this->getTable(), ['inline_css' => App::serialize($code)], $this->getIdName(), $this->getId());
	}
}
