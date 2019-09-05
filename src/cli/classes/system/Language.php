<?php
/**
* The System's Language Class
* @package Venus
*/

namespace Venus\Cli\System;

use Venus\App;

/**
* The System's Language Class
*/
class Language extends \Venus\System\Language
{

	/**
	* @see \Venus\System\Language\getLid()
	* {@inheritDoc}
	*/
	protected function getLid() : int
	{
		return (int)$this->app->config->language_default;
	}
}
