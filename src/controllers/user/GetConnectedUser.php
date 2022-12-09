<?php
declare(strict_types=1);

namespace Controllers\User;

require_once('src/model/User.php');

use Model\User;
use Model\UserRepository;

class GetConnectedUser {
	public function execute(array $input): ?User {
		return (new UserRepository())->getUserById($input['user_id'] ?? -1);
	}
}
