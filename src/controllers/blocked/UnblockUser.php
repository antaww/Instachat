<?php
declare(strict_types=1);

namespace Controllers\Blocked;

use Model\BlockedRepository;
use Model\User;
use RuntimeException;

class UnblockUser {
	public function execute(User $connected_user, array $input): void {
		if (!isset($input['blocked_id'])) throw new RuntimeException('Invalid input');
		(new BlockedRepository())->unblockUser($connected_user->id, $input['blocked_id']);
	}
}
