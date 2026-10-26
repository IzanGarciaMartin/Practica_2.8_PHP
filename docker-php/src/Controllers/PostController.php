<?php

namespace App\Controllers;

use App\Models\Post;

class PostController
{
    private Post $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }

    public function index(): array
    {
        return $this->postModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->postModel->getById($id);
    }

    public function search(string $term): array
    {
        return $this->postModel->search(trim($term));
    }

    public function create(
        int $userId,
        string $title,
        string $body,
        string $category
    ): array {
        $title = trim($title);
        $body = trim($body);
        $category = trim($category);

        if (
            $title === '' ||
            $body === '' ||
            $category === ''
        ) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios.',
            ];
        }

        if (
            mb_strlen($title) > 150 ||
            mb_strlen($category) > 100
        ) {
            return [
                'success' => false,
                'message' => 'El título o la categoría son demasiado largos.',
            ];
        }

        $this->postModel->insert([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'category' => $category,
        ]);

        return [
            'success' => true,
            'message' => 'Publicación creada correctamente.',
        ];
    }

    public function update(
        int $postId,
        int $userId,
        string $title,
        string $body,
        string $category
    ): array {
        $post = $this->postModel->getById($postId);

        if ($post === null) {
            return [
                'success' => false,
                'message' => 'La publicación no existe.',
            ];
        }

        if ((int) $post['user_id'] !== $userId) {
            return [
                'success' => false,
                'message' => 'No tienes permiso para editar esta publicación.',
            ];
        }

        $title = trim($title);
        $body = trim($body);
        $category = trim($category);

        if (
            $title === '' ||
            $body === '' ||
            $category === ''
        ) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios.',
            ];
        }

        if (
            mb_strlen($title) > 150 ||
            mb_strlen($category) > 100
        ) {
            return [
                'success' => false,
                'message' => 'El título o la categoría son demasiado largos.',
            ];
        }

        $this->postModel->update($postId, [
            'title' => $title,
            'body' => $body,
            'category' => $category,
        ]);

        return [
            'success' => true,
            'message' => 'Publicación actualizada correctamente.',
        ];
    }

    public function delete(int $postId, int $userId): array
    {
        $post = $this->postModel->getById($postId);

        if ($post === null) {
            return [
                'success' => false,
                'message' => 'La publicación no existe.',
            ];
        }

        if ((int) $post['user_id'] !== $userId) {
            return [
                'success' => false,
                'message' => 'No tienes permiso para borrar esta publicación.',
            ];
        }

        $this->postModel->delete($postId);

        return [
            'success' => true,
            'message' => 'Publicación eliminada correctamente.',
        ];
    }
}
