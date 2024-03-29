<?php
/**
* The Controller Class
* @package Venus
*/

namespace Venus;

/**
* The Controller Class
* Implements the Controller functionality of the MVC pattern
*/
abstract class Controller extends \Mars\Controller
{
	use MvcTrait;

	/**
	* @var string $name The name of the controller
	*/
	public string $name = '';

	/**
	* @var string $class The class of the controller
	*/
	public string $class = '';

	/**
	* @var string $path The controller's document's path. Alias for $this->document->path
	*/
	public string $path = '';

	/**
	* @var string $path_url The controller's document path's url. Alias for $this->document->path_url
	*/
	public string $path_url = '';

	/**
	* @var string $base_url The controller's document url. Alias for $this->document->base_url
	*/
	public string $base_url = '';

	/**
	* @var Document $document The document the controller belongs to
	*/
	public Document $document;

	/**
	* @var array $params The document's params
	*/
	public array $params = [];

	/**
	* Builds the controller
	* @param Document $document The document the controller belongs to
	*/
	public function __construct(Document $document)
	{
		$this->app = $this->getApp();

		$this->prepare($document);
		$this->init();
	}

	/**
	* Prepares the controller
	* @param Document $document The document the controller belongs to
	*/
	protected function prepare(Document $document = null)
	{
		parent::prepare();

		$this->document = $document;
		$this->name = $this->document->controller_name;
		$this->class = $this->document->controller_class;

		$this->params = $this->document->params;
		$this->prefix = $this->getPrefix();

		$this->path = $this->document->path;
		$this->url = $this->document->url;
		$this->path_url = $this->document->path_url;
		$this->base_url = $this->document->base_url;

		if ($this->name != $this->document->name) {
			$this->url = $this->app->uri->build($this->base_url, [$this->app->config->controller_param => $this->name]);
		}
	}

	/**
	* Loads a language file
	* @param string $file The name of the language file to load. If empty the default (index) file is loaded
	* @return $this
	*/
	public function loadLanguage(string $file = '')
	{
		$this->document->loadLanguage($file);

		return $this;
	}

	/**
	* Loads a css file
	* @param string $file The name of the css file to load
	* @return $this
	*/
	public function loadCss(string $file)
	{
		$this->document->loadCss($file);

		return $this;
	}

	/**
	* Loads a javascript file
	* @param string $file The name of the javascript file to load
	* @return $this
	*/
	public function loadJavascript(string $file)
	{
		$this->document->loadJavascript($file);

		return $this;
	}

	/**
	* Loads an object
	* This function only includes the file,it does not instantiate the object!
	* @param string $object The name of the object to load
	* @return $this
	*/
	public function loadObject(string $object = '')
	{
		$this->document->loadObject($object);

		return $this;
	}

	/**
	* Alias for load_object
	* @param string $object The name of the object to load
	* @return $this
	*/
	public function loadObjects(string $object = '')
	{
		$this->document->loadObject($object);

		return $this;
	}

	/**
	* Loads the model and returns an instance of $model_class
	* @param string $model The name of the model (must not include the .php extension)
	* @param string $name The name of the extension for which to return the model
	* @return Model Returns the instantiated model object
	*/
	public function getModel(string $model = '', string $name = '') : Model
	{
		if (!$model && $this->name) {
			$model = $this->name;
		}

		$this->model = $this->document->getModel($model, $name);

		return $this->model;
	}

	/**
	* Loads the view and returns an instance of $view_class
	* @param string $view The name of the view (must not include the .php extension)
	* @param string $name The name of the extension for which to return the view
	* @return View Returns the instantiated view object
	*/
	public function getView(string $view = '', string $name = '') : View
	{
		if (!$view && $this->name) {
			$view = $this->name;
		}

		$this->view = $this->document->getView($view, $name, $this);

		return $this->view;
	}
}
