<?php

namespace Controllers\Friend;

use Model\BlockedRepository;
use Model\User;
use RuntimeException;

class UnblockWord
{
    public function execute(User $connected_user, array $input): void
    {
        $friendRepository = new BlockedRepository();
        if (!isset($input['blocked_id'])) throw new RuntimeException('Invalid input');
        $friendRepository->unblockWord($connected_user->id, $input['blocked_id']);
    }
}