<?php
declare(strict_types=1);

namespace Controllers;

require_once 'src/controllers/comments/DeleteComment.php';
require_once 'src/controllers/posts/DeletePost.php';
require_once 'src/controllers/reactions/UnReact.php';
require_once 'src/controllers/users/DeleteUser.php';

use Controllers\Posts\DeletePost;
use Controllers\Reaction\UnReact;
use Controllers\Users\DeleteUser;
use Models\CommentRepository;
use Models\User;
use RuntimeException;
use Src\Controllers\comments\DeleteComment;
use function strtolower;

/**
 * Enum DeleteType is an enum that represents the type of the delete
 */
enum DeleteType: int {
	case POST = 0;
	case COMMENT = 1;
	case USER = 2;
	case REACTION = 3;

	/**
	 * fromName is a function that returns the DeleteType from a string
	 * @param string $name - the name of the DeleteType
	 * @return ?DeleteType - the DeleteType or null if not found
	 */
	public static function fromName(string $name): ?self {
		$delete_types = self::cases();
		foreach ($delete_types as $delete_type) {
			if (strtolower($delete_type->name) === strtolower($name)) {
				return $delete_type;
			}
		}
		return null;
	}
}

/**
 * Class Delete is a controller that deletes an entity
 * @package Controllers
 */
class Delete {
	/**
	 * execute is the function that deletes an entity
	 * @param User $connected_user - the user that wants to delete the entity
	 * @param array $input - the input of the request
	 * @return void - redirects to the home page
	 */
	public function execute(User $connected_user, array $input, string $type): void {
		$type = DeleteType::fromName($type) ?? throw new RuntimeException('Invalid input');

		switch ($type) {
			case DeleteType::POST:
				$post_id = (float)$input['post_id'];
				(new DeletePost())->execute($post_id);
				break;

			case DeleteType::COMMENT:
				$comment = (new CommentRepository())->getCommentById((float)$input['comment_id']);
				if ($comment === null) throw new RuntimeException('Invalid input');
				(new DeleteComment())->execute($comment);
				break;

			case DeleteType::USER:
				(new DeleteUser())->execute($connected_user, $input);
				break;

			case DeleteType::REACTION:
				$reaction_id = (float)$input['id'];
				(new UnReact())->execute($connected_user, $reaction_id);
				break;
		}
	}
}
