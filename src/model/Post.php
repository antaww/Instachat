<?php
declare(strict_types=1);

namespace Model;

require_once('src/lib/DatabaseConnection.php');

use Database\DatabaseConnection;
use DateTime;
use PDO;
use Utils\Blob;

enum Emotion: int
{
    case HAPPY = 1;
    case FUNNY  = 2;
    case DOUBTFUL = 3;
    case SAD = 4;
    case ANGRY = 5;
    case LOVE = 6;

    public function display(): string
    {
        return match ($this) {
            self::HAPPY => '😁',
            self::FUNNY => '🤣',
            self::DOUBTFUL => '🤔',
            self::SAD => '😭',
            self::ANGRY => '😡',
            self::LOVE => '😍',
        };
    }

    public static function fromInt(int $value): self
    {
        return match ($value) {
            default => self::HAPPY,
            2 => self::FUNNY,
            3 => self::DOUBTFUL,
            4 => self::SAD,
            5 => self::ANGRY,
            6 => self::LOVE,
        };
    }
}

class Post
{
    public DateTime $creation_date;
    public Emotion $emotion;
    public ?Blob $image;

    public function __construct(
        public float  $author_id,
        public string $content,
        public bool   $deleted,
        public float  $id,
        string        $creation_date,
        int           $emotion,
        ?string       $image
    )
    {
        $this->creation_date = date_create_from_format('U', $creation_date);
        $this->emotion = Emotion::fromInt($emotion);
        if ($image !== null) {
            $this->image = new Blob($image);
        }
    }
}

class PostRepository
{
    public PDO $databaseConnection;

    public function __construct()
    {
        $this->databaseConnection = (new DatabaseConnection())->getConnection();
    }

    public function addPost(string $content, int $author_id, ?string $photo, int $emotion): void
    {
        $statement = $this->databaseConnection->prepare('INSERT INTO posts (content, author_id, photo, emotion) VALUES (:content, :author_id, :photo, :emotion)');
        $statement->execute(compact('content', 'author_id', 'photo', 'emotion'));
    }

    public function deletePost(float $id): void
    {
        $statement = $this->databaseConnection->prepare('UPDATE posts SET deleted = true WHERE id = :id');
        $statement->execute(compact('id'));
    }

    public function getFeed(float $user_id): array
    {
        $statement = $this->databaseConnection->prepare('SELECT * FROM posts WHERE author_id IN (SELECT requested_id FROM friends WHERE requester_id = :author_id AND accepted = true) ORDER BY creation_date DESC');
        $statement->execute(compact('user_id'));
        return $statement->fetchObject(Post::class);
    }

    public function getPost(float $id): Post
    {
        $statement = $this->databaseConnection->prepare('SELECT * FROM posts WHERE id = :id');
        $statement->execute(compact('id'));
        return $statement->fetchObject(Post::class);
    }

    public function getPostContaining(string $content): array
    {
        $statement = $this->databaseConnection->prepare('SELECT * FROM posts WHERE content LIKE :content ORDER BY creation_date DESC');
        $statement->execute(compact('content'));
        return $statement->fetchObject(Post::class);
    }

    public function getPostsByUser(float $author_id): array
    {
        $statement = $this->databaseConnection->prepare('SELECT * FROM posts WHERE author_id = :author_id ORDER BY creation_date DESC');
        $statement->execute(compact('author_id'));
        return $statement->fetchObject(Post::class);
    }

    public function updateEmotion(float $id, Emotion $emotion): void
    {
        $statement = $this->databaseConnection->prepare('UPDATE posts SET emotion = :emotion WHERE id = :id');
        $statement->execute(['id' => $id, 'emotion' => $emotion->value]);
    }


    public function getTrends(): array
    {
        $statement = $this->databaseConnection->prepare('SELECT content FROM posts WHERE creation_date > DATE_SUB(NOW(), INTERVAL 1 DAY) AND deleted = false');
        $statement->execute();
        $posts = $statement->fetchAll(PDO::FETCH_COLUMN);
        $words = [];
        foreach ($posts as $post) {
            $words = array_merge($words, explode(' ', $post));
        }
        $words = array_filter($words, fn($word) => strlen($word) > 3);
        $words = array_count_values($words);
        arsort($words);
        return array_slice($words, 0, 10);
    }
}