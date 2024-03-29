<?php

namespace Bin;

use Venus\App;

class User extends Command
{
	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [
		'create' => [
							'create', 'Creates an user', ['--usergroup-id' => 'The usergroup id'], ['user:create [--usergroup-id=<usergroup-id>] <username> <password> <email>']
						],
		'update' => [
							'update', 'Updates an user', ['--username' => 'The username', '--email' => 'The email', '--password' => 'The password', '--usergroup-id' => 'The usergroup id'],
							['user:update [--username=<username>] [--email=<email>] [--password=<password>] [--usergroup-id=<usergroup-id>] <current_username>']
						],
		'delete' => [
							'delete', 'Deletes an user', [], ['user:delete <username>']
						],
	];

	/**
	* Creates an user
	*/
	public function create()
	{
		$args = 3;

		[$username, $password, $email] = $this->getArguments($args);

		if (!$this->checkArguments($args)) {
			$this->errorArguments($args);
		}

		$user = new \Venus\User(null);
		$user->skipValidationRule('ip');

		$user->username = $username;
		$user->password_clear = $password;
		$user->email = $email;

		if ($this->isOption('usergroup-id')) {
			$user->usergroup_id = (int)$this->getOption('usergroup-id');
		}

		if (!$user->insert()) {
			$this->app->lang->loadFile('user');
			$this->error(App::__($user->getErrors()));
		}

		$this->done();
	}

	public function update()
	{
		$username = $this->getArgument();

		if (!$username) {
			$this->errorArguments();
		}

		$user = new \Venus\User;
		$user->loadByUsername($username);
		if (!$user->isValid()) {
			$this->app->lang->loadFile('user');
			$this->error(App::__('user_doesnt_exist'));
		}

		$user->skipValidationRule('ip');

		$options = $this->getOptions(['username', 'email', 'usergroup-id']);
		$user->bind($options);

		if ($this->isOption('password')) {
			$user->password_clear = $this->getOption('password');
		}

		if (!$user->update()) {
			die("ooooo");
			$this->app->lang->loadFile('user');
			$this->error(App::__($user->getErrors()));
		}

		$this->done();
	}

	/**
	* Deletes an user
	*/
	public function delete()
	{
		$username = $this->getArgument();
		if (!$username) {
			$this->errorArguments();
		}

		$user = new \Venus\User;
		$user->loadByUsername($username);

		if (!$user->isValid()) {
			$this->app->lang->loadFile('user');
			$this->error(App::__('user_doesnt_exist'));
		}

		$user->delete();

		$this->done();
	}
}
