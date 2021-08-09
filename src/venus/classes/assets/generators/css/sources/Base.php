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

	protected Reader $reader;

	protected Writer $writer;

	public function __construct(App $app)
	{
		$this->app = $app;
		$this->reader = new Reader($this->app);
		$this->writer = new Writer($this->app);
	}

}