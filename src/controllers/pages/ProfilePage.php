<?php
declare(strict_types=1);

namespace Controllers\Pages;

require_once 'src/controllers/reactions/GetReactionsByAuthor.php';

use Controllers\Reaction\GetReactionsByAuthor;

class ProfilePage {
	public function execute(): void {
		global $connected_user, $user, $friend_list, $friend_requests, $sent_requests, $friendship, $reactions_list;

		$reactions_list = (new GetReactionsByAuthor())->execute($user->id);

		if ($connected_user->id !== $user->id) {
			foreach ($friend_list as $friend) {
				if ($user->id === $friend->requesterId && $connected_user->id === $friend->requestedId || $user->id === $friend->requestedId && $connected_user->id === $friend->requesterId) {
					$friendship = 1;
					break;
				}
			}
			foreach ($friend_requests as $friend) {
				if ($user->id === $friend->requesterId) {
					$friendship = 2;
					break;
				}
			}
			foreach ($sent_requests as $friend) {
				if ($user->id === $friend->requestedId) {
					$friendship = 3;
					break;
				}
			}
		}
		require_once 'templates/profile.php';
	}
}
