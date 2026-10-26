<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Post;

class CommentController
{
    private Comment $commentModel;
    private Post $postModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
        $this->postModel = new Post();
    }

    public function index(int $postId): array
    {
        return $this->commentModel->getByPostId($postId);
    }

    public function create(
        int $postId,
        int $userId,
        string $body
    ): array {
        $body = trim($body);

        if ($body === '') {
            return [
                'success' => false,
                'message' => 'El comentario no puede estar vacío.',
            ];
        }

        if (mb_strlen($body) > 5000) {
            return [
                'success' => false,
                'message' => 'El comentario es demasiado largo.',
            ];
        }

        if ($this->postModel->getById($postId) === null) {
            return [
                'success' => false,
                'message' => 'La publicación no existe.',
            ];
        }

        $this->commentModel->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'body' => $body,
        ]);

        return [
            'success' => true,
            'message' => 'Comentario publicado correctamente.',
        ];
    }

    public function delete(
        int $commentId,
        int $userId
    ): array {
        $comment = $this->commentModel->getById($commentId);

        if ($comment === null) {
            return [
                'success' => false,
                'message' => 'El comentario no existe.',
            ];
        }

        if ((int) $comment['user_id'] !== $userId) {
            return [
                'success' => false,
                'message' => 'No tienes permiso para borrar este comentario.',
            ];
        }

        $this->commentModel->delete($commentId);

        return [
            'success' => true,
            'message' => 'Comentario eliminado correctamente.',
        ];
    }
}
