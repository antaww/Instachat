<?php use Controllers\Post\GetComments;
use Model\Emotion;

$css = ['homepage.css'];
$title = 'Instachat';

ob_start();
require_once('toolbar.php');
?>
<?php global $connected_user; ?>
<?php global $posts; ?>
<?php if (isset($connected_user)) { ?>
    <div class="homepage-container">
        <div class="title">
            <h1>Accueil</h1>
        </div>
        <div class="chat-container">
            <div class="chat-avatar">
                <img src="../static/images/logo.png" alt="avatar">
            </div>
            <div class="chat-right">
                <form class="post-form" action="/chat" method="post">
                    <textarea class="chat-area" placeholder="Chatter quelque chose..." name="content"
                              maxlength="400"></textarea>
                    <div class="chat-form-bottom">
                        <button class="chat-image-btn">
                            <span class="material-symbols-outlined chat-action-buttons-color">image</span>
                        </button>
                        <div class="emotions">
                            <?php
                            for ($i = 1; $i < count(Emotion::cases()) + 1; $i++) {
                                ?>
                                <label>
                                    <input type="radio" name="emotion" class="emotion"
                                           value="<?= $i ?>" <?= $i === 1 ? 'checked' : '' ?> required hidden/>
                                    <span class="emoji-span"><?= Emotion::cases()[$i - 1]->display() ?></span>
                                </label>
                                <?php
                            }
                            ?>
                        </div>
                        <button class="chat-btn" type="submit">Chat</button>
                    </div>
                </form>
            </div>

        </div>
        <div class="feed-container">
            <?php
            if (count($posts) > 0) {
                global $post;
                global $comments;
                foreach ($posts as $post) {
                    $comments = (new GetComments())->execute($post->id);
                    require('post.php');
                }
            } else {
                ?>
                <div class="no-post">
                    <p>Il n'y a pas de post pour le moment</p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

<?php } ?>
<?php
$content = ob_get_clean();
require_once('layout.php');
