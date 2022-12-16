<?php
declare(strict_types=1);

namespace Controllers\Blocked;

use Models\BlockedRepository;
use Models\User;
use RuntimeException;
use function Lib\Utils\redirect;

class UnblockWord {
	public function execute(User $connected_user, array $input): void {
		if (!isset($input['blocked-word'])) throw new RuntimeException('Invalid input');
		(new BlockedRepository())->unblockWord($connected_user->id, $input['blocked-word']);
		redirect('/options');
	}
}
