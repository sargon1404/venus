<?php

return [
	'user_doesnt_exist' => "The specified user doesn't exist",
	'user_username_missing' => 'The username is missing',
	'user_username_exists' => 'A user with this username already exists',
	'user_username_short' => "The username is to short. It must contain at least {$this->app->config->users_min_username} chars.",
	'user_username_invalid' => 'Your username is invalid. It can only contain letters, numbers and these chars: _ - .',
	'user_password_missing' => 'The password is missing',
	'user_password_short' => "The password is to short. It must contain at least {$this->app->config->users_min_password} chars.",
	'user_email_missing' => 'The email is missing',
	'user_email_invalid' => 'The email of the  user is invalid',
	'user_email_exists' => 'A user with this email already exists',
	'user_ip_to_many' => 'The max. number of allowed accounts have already been registered from this IP',
	'user_usergroup_doesnt_exist' => "The specified usergroup doesn't exist",
];
