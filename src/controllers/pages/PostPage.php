<?php

namespace Controllers\Pages;

use Models\PostRepository;
use function Lib\Utils\redirect;

class PostPage {
	public function execute(array $input) {
		$post_id = $input['id'];
		if ($post_id === null) redirect('/');

		global $post;
		$post = (new PostRepository())->getPost($post_id);

		require_once 'templates/post.php';
	}
}