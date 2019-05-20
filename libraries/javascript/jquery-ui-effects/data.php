<?php

return [
	'files' => ['jquery-ui.min.js'],
	'location' => 'head',
	'priority' => 10000,
	'async' => false,
	'defer' => false,

	'dependencies' => [
		'files' => ['css/jquery-ui.min.css'],
		'location' => 'head',
		'priority' => 10000,
	]
];