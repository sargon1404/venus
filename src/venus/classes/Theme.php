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
	public ?string $parent_name = '';

	/**
	* @var string $parent_templates_path The filesystem path for the theme's parent templates folder
	*/
	public string $parent_templates_path = '';

	/**
	* @var string|array $templates Array with the keys listing the available templates of the theme
	*/
	public string|array $templates = [];

	/**
	* @var string|array $parent_templates Array with the keys listing the available templates of the parent theme
	*/
	public string|array|null $parent_templates = [];

	/**
	* @var bool $has_javascript_dir True if the theme has a javascript dir
	*/
	public bool $has_javascript_dir = false;

	/**
	* @var bool $parent_has_javascript_dir True if the parent theme has a javascript dir
	*/
	public ?bool $parent_has_javascript_dir = false;

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
	* @var mixed $params The theme's params
	*/
	public $params = '';

	/**
	* @var array $parent_params The theme's parent params
	*/
	public $parent_params = [];

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
	* @param int|array|object $theme The theme's id/data
	*/
	public function __construct(int|array|object $theme = 0)
	{
		parent::__construct($theme);

		$this->app->plugins->run('theme_construct', $this, $theme);
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
			p.params as parent_params
			FROM {$table} as t
			LEFT JOIN {$table} as p ON t.parent = p.tid
			WHERE t.tid = {$tid} AND t.status = 1");

		return $this->app->db->getRow();
	}

	/**
	* @see \Venus\Extensions\Extension::prepare()
	* {@inheritdoc}
	*/
	protected function prepare()
	{
		parent::prepare();

		$this->prepareTemplates();
	}

	/**
	* @see \Venus\Extensions\Extension::preparePaths()
	* {@inheritdoc}
	*/
	protected function preparePaths()
	{
		$this->prepareBasePaths();
	}

	/**
	* Prepares the theme's params
	*/
	protected function prepareParams()
	{
		$this->params_data = $this->app->serializer->unserialize($this->params);
		$this->parent_params_data = $this->app->serializer->unserialize($this->parent_params);

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
			$this->parent_templates_path = $this->getPath($this->parent_name) . App::EXTENSIONS_DIRS['templates'];
		}

		$this->templates = $this->app->serializer->unserialize($this->templates);
		$this->parent_templates = $this->app->serializer->unserialize($this->parent_templates);
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

		return $this->params->$key;
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

		return $this->param->$key;
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
			return is_file($this->templates_path . $filename);
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
			return is_file($this->parent_templates_path . $filename);
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
		$this->app->file->listDir($this->templates_path, $dirs, $templates, false, true);

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

		$this->app->db->updateById($this->getTable(), ['inline_js' => $this->app->serializer->serialize($code)], $this->getId(), $this->getIdName());
	}

	/**
	* Updates the theme's css inline code
	* @param array $code The inline code
	*/
	public function updateInlineCss(array $code = [])
	{
		$code = $this->app->filter->trim($code);

		$this->app->db->updateById($this->getTable(), ['inline_css' => $this->app->serializer->serialize($code)], $this->getId(), $this->getIdName());
	}
}
