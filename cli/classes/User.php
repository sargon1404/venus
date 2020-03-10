<?php

namespace Cli;

class User extends Command
{
	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [
		'create' => [
							'create', 'Creates an user', ['--username, -u' => 'The username', '--password, -p' => 'The password', '--email, -e' => 'The email'],
						 	['user:create <username> <password> <email>', 'user:create -u <username> -p <password> -e <email>', 'user:create -user=<username> --password=<password> -email=<email>']
						],
	];



	/**
	* Creates an user
	* @param array $options
	*/
	public function create(array $options)
	{
		[$username, $password, $email] = $this->getOptionsList(3);

		if (!$username || !$password || !$email) {
			if (!$this->checkOptions(['username' => ['u', 'username'], 'password' => ['p', 'password'], 'email' => ['e', 'email']])) {
				$this->errorOptions();
			}

			$username = $this->getOption(['u', 'user']);
			$password = $this->getOption(['p', 'password']);
			$email = $this->getOption(['e', 'email']);
		}

		$user = new \Venus\User;
		$user->username = $username;
		$user->password_clear = $password;
		$user->email = $email;
		//$user->skipRules('ip');

		print_r($user);die;

		if (!$user->insert()) {

		}

		$this->done();
	}
}
