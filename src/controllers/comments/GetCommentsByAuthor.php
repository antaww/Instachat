<?php

namespace Controllers\comments;

use Controllers\Blocked\GetBlockedWords;
use Models\CommentRepository;
use function Lib\Utils\filterBlockedPosts;

class GetCommentsByAuthor {

	public function execute(float $author_id): array {
		global $connected_user;
		$offset = (int)($_GET['offsetComments'] ?? 0);
		$comments = (new CommentRepository())->getCommentsByAuthor($author_id, $offset);

		return filterBlockedPosts($connected_user, $comments);
	}

}