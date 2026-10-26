<?php

namespace App\Models;

class Post extends BaseModel
{
    public function __construct()
    {
        parent::__construct('posts');
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            'SELECT posts.*, users.username
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC'
        );
    }

    public function getById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT posts.*, users.username
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            WHERE posts.id = ?',
            [$id]
        );
    }

    public function getByUserId(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM posts
            WHERE user_id = ?
            ORDER BY created_at DESC',
            [$userId]
        );
    }

    public function search(string $term): array
    {
        $term = '%' . $term . '%';

        return $this->fetchAll(
            'SELECT posts.*, users.username
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            WHERE posts.title LIKE ?
                OR posts.body LIKE ?
                OR posts.category LIKE ?
            ORDER BY posts.created_at DESC',
            [$term, $term, $term]
        );
    }
}
