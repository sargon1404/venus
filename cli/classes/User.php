<?php

namespace Cli;

class User extends Command
{
	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [
		'create' => ['create', 'Creates an user', ['-u' => 'The username', '-p' => 'The password', '-e' => 'The email'], 'user:create -u=<username> -p=<password> -e=<email>'],
	];



	/**
	* Creates an user
	* @param array $options
	*/
	public function create(array $options)
	{
		if (empty($options['u'])) {
			$this->errorAndDie("Please input the username [-u]");
		}
		if (empty($options['p'])) {
			$this->errorAndDie("Please input the password [-p]");
		}
		if (empty($options['e'])) {
			$this->errorAndDie("Please input the email [-e]");
		}

		$user = new \Venus\User;
		$user->username = $options['u'];
		$user->password_clear = $options['p'];
		$user->email = $options['e'];
		//$user->skipRules('ip');

		if (!$user->insert()) {

		}

		$this->done();
	}
}
