<?php

namespace App\Models;

class Comment extends BaseModel
{
    public function __construct()
    {
        parent::__construct('comments');
    }

    public function getByPostId(int $postId): array
    {
        return $this->fetchAll(
            'SELECT comments.*, users.username
            FROM comments
            INNER JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = ?
            ORDER BY comments.created_at ASC',
            [$postId]
        );
    }

    public function getByUserId(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM comments
            WHERE user_id = ?
            ORDER BY created_at DESC',
            [$userId]
        );
    }
}
