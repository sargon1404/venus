<?php
/**
* The Css Base Source Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css\Sources;

use Venus\App;
use Venus\Assets\Generators\Css\Reader;
use Venus\Assets\Generators\Css\Writer;

/**
* The Css Base Source Class
*/
abstract class Base
{
	use \Venus\AppTrait;

	/**
	* @var Reader $reader The reader object
	*/
	protected Reader $reader;

	/**
	* @var Writer $writer The writer object
	*/
	protected Writer $writer;

	/**
	* Base constructor for the Source objects
	* @var App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->reader = new Reader($this->app);
		$this->writer = new Writer($this->app);
	}
}
