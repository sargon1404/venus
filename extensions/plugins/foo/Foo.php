<?php
namespace Cms\Plugins\Foo;

class Foo extends \Venus\Plugin
{
	protected array $hooks = [
		'app_boot' => 'bootMinimum',
		'db_construct' => 'dbConstruct'
	];

	public function bootMinimum()
	{
		//echo "yyyyy";
		return 'bbbbbbb';
	}
}