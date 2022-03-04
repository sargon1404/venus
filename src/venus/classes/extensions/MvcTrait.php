<?php
/**
* The Extension's MVC Trait
* @package Venus
*/

namespace Venus\Extensions;

use Venus\App;
use Venus\Controller;
use Venus\Model;
use Venus\View;

/**
* The Extension's MVC Trait
* Trait implementing the MVC patter for extensions
*/
trait MvcTrait
{
	/**
	* @var Controller $controller The currently loaded controller of this extension
	*/
	public Controller $controller;

	/**
	* @var string $controller_name The name of the currently loaded controller
	*/
	public string $controller_name = '';

	/**
	* @var string $controller_class The class of the currently loaded controller
	*/
	public string $controller_class = '';

	/**
	* @var string $controller_parent The name of the extension which loaded the controller. Will be non-empty only if the parent is not the current extension
	*/
	public string $controller_parent = '';

	/**
	* @var string $controller_action The action to be returned when calling get_action. If empty $this->app->request->get_action is returned
	*/
	public string $controller_action = '';

	/**
	* @var bool $is_mvc True an extension implements mvc
	*/
	protected bool $is_mvc = true;

	/**
	* Returns the action to be executed
	* @return string
	*/
	protected function getAction() : string
	{
		if ($this->controller_action) {
			return $this->controller_action;
		}

		return $this->app->request->getAction();
	}

	/**
	* Sets the action to be executed
	* @param string $action The action to be executed
	* @return $this
	*/
	protected function setAction(string $action)
	{
		$this->controller_action = $action;

		return $this;
	}

	/**
	* Returns a MVC class name
	* @param string $dir The dir from where to load the class
	* @param string $class_name The class name
	* @return string The class name
	*/
	protected function getMvcClass(string $dir, string $class_name) : string
	{
		$namespace_path = str_replace("/", "\\", ucfirst($dir));

		return $namespace_path . App::getClass($class_name);
	}

	/**
	* Loads the controller and returns the instance
	* @param string $controller The name of the controller (must not include the .php extension)
	* @param string $name The name of the extension for which to return the controller. If empty, $this->name is used
	* @return object Returns the instantiated controller object
	*/
	public function getController(string $controller = '', string $name = '') : Controller
	{
		if (!$name) {
			$name = $this->name;
		}
		if (!$controller) {
			$controller = $name;
		}

		$controller_class = $this->getMvcClass(App::EXTENSIONS_DIRS['controllers'], $controller);

		$this->controller_name = $controller;
		$this->controller_class = $controller_class;

		$class_name = static::$namespace . App::getClass($name) . "\\" . $controller_class;

		$this->controller = new $class_name($this);

		return $this->controller;
	}

	/**
	* Loads the controller and returns the instance of $controller_class.
	* The difference between createController() and getController() is that createController considers $controller as unsafe and will perform additional checkings, thus is possible to get the name of the controller from request data
	* Whenever possible getController() should be used
	* @param string $controller The name of the controller (must not include the .php extension)
	* @param string $name The name of the extension for which to return the controller. If empty, $this->name is used
	* @return object Returns the instantiated controller object
	*/
	public function createController(string $controller = '', string $name = '') : Controller
	{
		if (!$name) {
			$name = $this->name;
		}
		if ($controller) {
			$filename = $this->getDir($name) . App::EXTENSIONS_DIRS['controllers'] . $this->app->filter->filename($controller) . '.php';
			if (!is_file($filename)) {
				$controller = '';
			}
		}

		return $this->getController($controller, $name);
	}

	/**
	* Loads the model and returns the instance
	* @param string $model The name of the model (must not include the .php extension)
	* @param string $name The name of the extension for which to return the model. If empty, $this->name is used
	* @return object Returns the instantiated model object
	*/
	public function getModel(string $model = '', string $name = '') : Model
	{
		if (!$name) {
			$name = $this->name;
		}
		if (!$model) {
			$model = $name;
		}

		$model_class = $this->getMvcClass(App::EXTENSIONS_DIRS['models'], $model);

		$class_name = static::$namespace . $name . "\\" . $model_class;

		return new $class_name;
	}

	/**
	* Loads the view and returns the instance
	* @param string $view The name of the view (must not include the .php extension). If empty the default (index) view is loaded
	* @param string $view_class The view's class.If empty, it will be determined from the name of the view
	* @param string $name The name of the extension for which to return the view. If empty, $this->name is used
	* @param Controller $controller The controller the view belongs to
	* @return object Returns the instantiated view object
	*/
	public function getView(string $view = '', string $name = '', ?Controller $controller = null) : View
	{
		if (!$name) {
			$name = $this->name;
		}
		if (!$view) {
			$view = $name;
		}

		$view_class = $this->getMvcClass(App::EXTENSIONS_DIRS['views'], $view);

		$class_name = static::$namespace . $name . "\\" . $view_class;

		return new $class_name($controller);
	}

	/**
	* Returns the mvc template base
	* @return string
	*/
	protected function getMvcTemplateBase() : string
	{
		if ($this->controller_parent) {
			return $this->controller_parent;
		}

		return '';
	}

	/**
	* Returns the mvc template layout
	* @return string
	*/
	protected function getMvcTemplateLayout() : string
	{
		if (isset($this->controller->view)) {
			return $this->controller->view->getLayout();
		} elseif ($this->controller_name && $this->controller_name != 'index') {
			return $this->controller_name;
		}

		return '';
	}
}
