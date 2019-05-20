<?php

return [
	'files' => ['css/bootstrap.min.css', 'css/bootstrap-theme.min.css'],
	'location' => 'head',
	'priority' => 10000,

	'dependencies' => [
		'files' => ['js/bootstrap.min.js'],
		'location' => 'head',
		'priority' => 10000,
		'async' => false,
		'defer' => false
	]
];