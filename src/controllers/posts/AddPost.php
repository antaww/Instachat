<?php
declare(strict_types=1);

namespace Controllers\Posts;

use Models\PostRepository;
use Models\User;
use RuntimeException;
use function Lib\Utils\redirect;

class AddPost {
	public function execute(User $connected_user, array $input): void {
		var_dump(strlen($_FILES['image-content']['tmp_name']) > 0);
		if (strlen($_FILES['image-content']['tmp_name']) > 0) {
			$image_src = $_FILES;
			$image_tmp = $image_src['image-content']['tmp_name'];
			$image_base64 = base64_encode(file_get_contents($image_tmp));
			$image_extension = pathinfo($image_src['image-content']['name'], PATHINFO_EXTENSION);
			$image = 'data:image/' . $image_extension . ';base64,' . $image_base64;
			if (!in_array($image_extension, ['png', 'jpeg', 'jpg'])) {
				$image = null;
			}
		}

		$chat = ['content', 'emotion'];
		foreach ($chat as $value) {
			if (!isset($input[$value])) throw new RuntimeException('Invalid input');
		}
		(new PostRepository())->addPost($input['content'], $connected_user->id, $image ?? '', (int)$input['emotion']);

		redirect('/');
	}
}
