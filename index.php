<?php
declare(strict_types=1);

require_once('src/controllers/Delete.php');
require_once('src/controllers/friends/GetFriendRequests.php');
require_once('src/controllers/friends/GetFriends.php');
require_once('src/controllers/friends/GetSentRequests.php');
require_once('src/controllers/friends/RemoveFriend.php');
require_once('src/controllers/friends/AcceptRequest.php');
require_once('src/controllers/friends/DeclineRequest.php');
require_once('src/controllers/friends/CancelRequest.php');
require_once('src/controllers/pages/AuthentificationPage.php');
require_once('src/controllers/pages/FriendPage.php');
require_once('src/controllers/pages/HomePage.php');
require_once('src/controllers/pages/OptionsPage.php');
require_once('src/controllers/pages/TrendPage.php');
require_once('src/controllers/pages/SearchTrendPage.php');
require_once('src/controllers/posts/AddPost.php');
require_once('src/controllers/posts/GetComments.php');
require_once('src/controllers/posts/GetFeed.php');
require_once('src/controllers/posts/GetTrends.php');
require_once('src/controllers/posts/GetPostContaining.php');
require_once('src/controllers/users/CreateUser.php');
require_once('src/controllers/users/GetConnectedUser.php');
require_once('src/controllers/users/GetUser.php');
require_once('src/controllers/users/LoginUser.php');
require_once('src/controllers/users/UpdatePassword.php');
require_once('src/controllers/users/UpdatePreferences.php');
require_once('src/controllers/users/UpdateUserInformation.php');
require_once('src/lib/utils.php');
require_once('src/lib/dateUtils.php');
require_once('src/models/Post.php');

use Controllers\Delete;
use Controllers\Friends\AcceptRequest;
use Controllers\Friends\CancelRequest;
use Controllers\Friends\DeclineRequest;
use Controllers\Friends\GetFriendRequests;
use Controllers\Friends\GetFriends;
use Controllers\Friends\GetSentRequests;
use Controllers\Friends\RemoveFriend;
use Controllers\Pages\AuthentificationPage;
use Controllers\Pages\FriendPage;
use Controllers\Pages\HomePage;
use Controllers\Pages\OptionsPage;
use Controllers\Pages\SearchTrendPage;
use Controllers\Posts\AddPost;
use Controllers\Posts\GetFeed;
use Controllers\Posts\GetPostContaining;
use Controllers\Posts\GetTrends;
use Controllers\Users\CreateUser;
use Controllers\Users\GetConnectedUser;
use Controllers\Users\LoginUser;
use Controllers\Users\UpdatePassword;
use Controllers\Users\UpdatePreferences;
use Controllers\Users\UpdateUserInformation;
use Models\User;
use function Lib\Utils\redirect;
use function Lib\Utils\redirect_if_method_not;

session_start();
date_default_timezone_set('Europe/Paris');

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? '';
	$uriSegments = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	$firstSegment = $uriSegments[1] ?? '';

	/**
	 * @var User $connected_user
	 */
	global $connected_user;
	$connected_user = (new GetConnectedUser())->execute($_SESSION);
	if ($connected_user !== null && $method === 'GET') {
		$_SESSION['expire'] = time() + 3600;
		if (time() > $_SESSION['expire']) {
			session_destroy();
		}
	}
	if ($connected_user === null && $firstSegment !== 'create' && $method === 'GET') {
		redirect('/create');
	}

	switch ($firstSegment) {
		default:
			global $posts, $trends;
			$posts = (new GetFeed())->execute($connected_user);
			$trends = (new GetTrends())->execute();
			(new HomePage())->execute();
			break;

		case 'getFeed':
			redirect_if_method_not('GET', '/');
			$posts = (new GetFeed())->execute($connected_user);
			global $post;
			foreach ($posts as $post) {
				require('templates/components/post.php');
			}

			exit();

		case 'create':
			if ($method === 'GET') (new AuthentificationPage())->execute();
			else (new CreateUser())->execute($_POST);
			break;

		case 'login':
			redirect_if_method_not('POST', '/');
			(new LoginUser())->execute($_POST);
			break;

		case 'logout':
			session_destroy();
			redirect('/');

		case 'chat':
			redirect_if_method_not('POST', '/');
			(new AddPost())->execute($connected_user, $_POST);
			break;

		case 'delete':
			redirect_if_method_not('POST', '/');
			(new Delete())->execute(array_merge($_POST, ['type' => $_GET['type'] ?? '']));
			break;

		case 'options':
			(new OptionsPage())->execute();
			break;

		case 'update-preferences':
			redirect_if_method_not('POST', '/options');
			(new UpdatePreferences())->execute($connected_user, $_POST);
			break;

		case 'update-password':
			redirect_if_method_not('POST', '/options');
			(new UpdatePassword())->execute($connected_user, $_POST);
			break;

		case 'update-user-information':
			redirect_if_method_not('POST', '/options');
			(new UpdateUserInformation())->execute($connected_user, $_POST);
			break;

		case 'friends':
			redirect_if_method_not('GET', '/');
			global $friend_list, $friend_requests, $sent_requests, $trends;
			$friend_list = (new GetFriends())->execute($connected_user);
			$friend_requests = (new GetFriendRequests())->execute($connected_user);
			$sent_requests = (new GetSentRequests())->execute($connected_user);
			$trends = (new GetTrends())->execute();
			(new FriendPage())->execute();
			break;

		case 'remove-friend':
			redirect_if_method_not('POST', '/friends');
			(new RemoveFriend())->execute($connected_user, $_POST);
			break;

		case 'accept-friend':
			redirect_if_method_not('POST', '/friends');
			(new AcceptRequest())->execute($connected_user, $_POST);
			break;

		case'decline-friend':
			redirect_if_method_not('POST', '/friends');
			(new DeclineRequest())->execute($connected_user, $_POST);
			break;

		case 'cancel-friend':
			redirect_if_method_not('POST', '/friends');
			(new CancelRequest())->execute($connected_user, $_POST);
			break;

		case 'search-trend':
			redirect_if_method_not('POST', '/');
			global $searched_trend, $searched_posts,  $trends;
			$searched_trend = $_POST['trend'];
			$searched_posts = (new GetPostContaining())->execute($_POST['trend']);
			$trends = (new GetTrends())->execute();
			(new SearchTrendPage())->execute();
			global $trend;
	}
} catch (Exception $exception) {
	echo '<pre>';
	echo 'Exception: ' . $exception->getMessage();
	$previous = $exception->getPrevious();

	while ($previous !== null) {
		echo '<br>Previous: ' . $previous->getMessage();
		$previous = $previous->getPrevious();
	}
	echo '</pre>';
}
