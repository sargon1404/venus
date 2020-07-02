<?php
/**
* The Options Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Options Class
*/
abstract class Options extends \Mars\Html\Input\Select
{
	/**
	* @var bool $show_global If true, will show the Global option
	*/
	public bool $show_global = false;

	/**
	* @var bool $show_no_change If true, will show the 'No Change' option
	*/
	public bool $show_no_change = false;

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		$options = $this->getOptions();

		if ($this->show_global) {
			$options = ['-1' => App::__('global')] + $options;
		}
		if ($this->show_no_change) {
			$options = ['.' => App::__('no_change')] + $options;
		}

		$this->options = $options;

		return parent::get();
	}

	/**
	* Returns the options
	* @return array
	*/
	protected function getOptions() : array
	{
		return [];
	}
}
