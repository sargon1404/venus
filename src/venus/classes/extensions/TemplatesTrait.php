<?php
/**
* The Extension's Templates Trait
* @package Venus
*/

namespace Venus\Extensions;

use Venus\App;

/**
* The Extension's Templates Trait
* Trait which allows extensions to load templates
*/
trait TemplatesTrait
{
	/**
	* Starts the extension's output
	*/
	protected function startOutput()
	{
		$this->app->theme->setObj($this);

		parent::startOutput();
	}

	/**
	* End the extension's output
	* @return string The generated output
	*/
	protected function endOutput()
	{
		$this->app->theme->restoreObj();

		return parent::endOutput();
	}

	/**
	* Loads a template from the extension's templates dir
	* Workflow: If first checks if the theme has a template for this extension in eg: extensions/blocks/profile/index.tpl, then checks the base theme (if any), then the extension's templates folder
	* @param string $template The name of the template to load
	* @param string $layout The name of the layout (subfolder of the templates dir) from where to load the template. If empty, the template will be loaded from the templates folder
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return string The contents of the template
	*/
	public function getTemplate(string $template, string $layout = '', string $name = '') : string
	{
		if (!$template) {
			return '';
		}

		$name = $this->getTemplateBase($name);
		$layout = $this->getTemplateLayout($layout);
		$cache_filename = $this->app->theme->getItemCacheFilename($this->getType(), $name, $template, $layout);

		$filename = $this->getTemplateFilename($name, $layout, $template);

		return $this->app->theme->getExtensionTemplate($filename, $cache_filename, $this->debug);
	}

	/**
	* Returns the filename of a $template. Should be overriden by the extension, for custom processing\
	* @param string $name The extension's name
	* @param string $layout The layout
	* @param string $template The template
	* @return string The template's filename
	*/
	protected function getTemplateFilename(string $name, string $layout, string $template) : string
	{
		$dir = App::sl($this->getContentDir());
		$name = App::sl($name);
		$layout = App::sl($layout);
		var_dump("aici");
		die;
		$template.= '.' . App::FILE_EXTENSIONS['templates'];

		if ($this->app->device->isMobile()) {
			$filename = $this->getTemplateFilenameForDevice($dir, $name, $layout, $template, $this->app->device->type);
			if ($filename) {
				return $filename;
			}

			//check in the mobile folder
			$filename = $this->getTemplateFilenameForDevice($dir, $name, $layout, $template, 'mobile');
			if ($filename) {
				return $filename;
			}
		}

		return $this->getTemplateFilenameForDevice($dir, $name, $layout, $template);
	}

	/**
	* Returns the template's filename for a device
	* @param string $dir The dir
	* @param string $name The name of the extension
	* @param string $layout The layout
	* @param string $template The template
	* @param string $device The device
	* @return string The template's filename for the given device
	*/
	protected function getTemplateFilenameForDevice(string $dir, string $name, string $layout, string $template, string $device = '') : string
	{
		if ($dir) {
			//check in the theme's extensions folder
			$filename = $this->app->theme->getExtensionTemplateFilename($dir, $name, $layout, $template, $device);

			if ($filename) {
				return $filename;
			}
		}

		$templates_dir = $this->dir . App::EXTENSIONS_DIRS['templates'];
		$filename = $layout . $this->app->device->getSubdir($device) . $template;

		if ($this->templateExists($filename)) {
			return $templates_dir . $filename;
		}

		if ($device != 'desktop') {
			return '';
		} else {
			//return the filename even if it doesn't exist, so the extension's dev. is aware of the missing template
			return $templates_dir . $filename;
		}
	}

	/**
	* Checks if a template exists
	* @param string $filename The template's filename *relative* to the templates folder
	* @return bool
	*/
	protected function templateExists(string $filename) : bool
	{
		return is_file($this->dir . App::EXTENSIONS_DIRS['templates'] . $filename);
	}

	/**
	* Returns the name of the extension from where a template must be loaded
	* @param string $name The name of the extension
	* @return string The template's base
	*/
	protected function getTemplateBase(string $name) : string
	{
		if (isset($this->is_mvc)) {
			$base = $this->getMvcTemplateBase();
			if ($base) {
				return $base;
			}
		}

		if ($name) {
			return $name;
		}

		return $this->name;
	}

	/**
	* Returns the current layout's name
	* @param string $layout The template's layout
	* @return string The template's layout
	*/
	protected function getTemplateLayout(string $layout) : string
	{
		if ($layout) {
			return $layout;
		}

		if (isset($this->is_mvc)) {
			return $this->getMvcTemplateLayout();
		}

		return '';
	}

	/**
	* Loads the template and outputs it.
	* @param string $template The name of the template
	* @param string $layout The name of the layout (subfolder of the templates dir) from where to load the template. If empty, the template will be loaded from the templates folder
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	*/
	public function render(string $template, string $layout = '', string $name = '')
	{
		echo $this->getTemplate($template, $layout, $name);
	}

	/**
	* Alias for render
	* @param string $template The name of the template
	* @param string $layout The name of the layout (subfolder of the templates dir) from where to load the template. If empty, the template will be loaded from the templates folder
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	*/
	public function renderTemplate(string $template, string $layout = '', string $name = '')
	{
		echo $this->getTemplate($template, $layout, $name);
	}
}
