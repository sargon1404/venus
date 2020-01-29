<?php

namespace Cli;

class Cache extends Command
{
	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [
		'all' => ['all', 'Caches all frontend and admin assets & data'],
		'libraries' => ['libraries', 'Caches the css and javascript libraries'],
		'css' => ['css', 'Caches the css code', ['-d' => 'Some desc', '-e' => 'Some desc 123']],
	];
		

	public function libraries(array $options)
	{
		$this->info('Building libraries...');
		
		$cache = new \Venus\Admin\Cache;
		$cache->buildLibraries();
	}
	
	public function css(array $options)
	{
		$this->info('Building css code...');
		
		$cache = new \Venus\Admin\Cache;
		$cache->buildCss();
	}
	
	protected function getAvailableActions() : array
	{
		return [
			'cache:all' => 'Caches all frontend and admin assets: libraries, css, javascript',
			'cache:libraries' => 'Caches the css and javascript libraries',
			'cache:css' => 'Caches the css code',
			'cache:css-frontend' => 'Caches the frontend css code',
			'cache:css-admin' => 'Caches the admin css code',
			'cache:javascript' => 'Caches the javascript code',
			'cache:javascript-frontend' => 'Caches the frontend javascript code',
			'cache:javascript-admin' => 'Caches the admin javascript code',
			
		];
	}
}
