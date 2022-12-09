<?php
declare(strict_types=1);

namespace Controllers\Users;

require_once('src/models/User.php');

use Models\UserRepository;
use RuntimeException;
use function Lib\Utils\redirect;

class CreateUser {
	public function execute(array $input): void {
		$sign_in = ['username', 'email', 'password', 'gender', 'birthdate'];
		foreach ($sign_in as $value) {
			if (!isset($input[$value])) throw new RuntimeException('Invalid input');
		}
		$user_exist = (new UserRepository())->isUserAlreadyRegistered($input['email'], $input['username']);
		if ($user_exist) {
			echo('User already exists');
			redirect('/');
		}
		$birth_date = date_create($input['birthdate']);
		(new UserRepository())->createUser($input['username'], $input['email'], $input['password'], $input['gender'], $birth_date);
		redirect('/create'); //to make sure the user logs in
	}
}