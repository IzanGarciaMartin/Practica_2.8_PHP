<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\CommentController;
use App\Models\Comment;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['_csrf_token'])) {
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

$router = new Router();
$authController = new AuthController();
$postController = new PostController();
$commentController = new CommentController();
$commentModel = new Comment();

$csrfToken = $_SESSION['_csrf_token'];

$render = function (string $view, array $data = []): void {
    $data['csrfToken'] = $_SESSION['_csrf_token'] ?? '';
    $data['currentUser'] = $_SESSION['user'] ?? null;

    extract($data, EXTR_SKIP);

    ob_start();
    require dirname(__DIR__) . '/src/Views/' . $view . '.php';
    $content = ob_get_clean();

    $pageTitle = $pageTitle ?? 'Tablón';

    require dirname(__DIR__) . '/src/Views/layouts/main.php';
};

$requireLogin = function (): void {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
};

$checkCsrf = function (): void {
    $sessionToken = $_SESSION['_csrf_token'] ?? '';
    $submittedToken = $_POST['_csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        $sessionToken === '' ||
        !hash_equals($sessionToken, $submittedToken)
    ) {
        http_response_code(403);
        exit('Solicitud no válida. Recarga la página e inténtalo de nuevo.');
    }
};

/*
 * Página principal y búsqueda.
 */
$router->get('/', function () use ($render, $postController) {
    $term = trim($_GET['q'] ?? '');

    $posts = $term !== ''
        ? $postController->search($term)
        : $postController->index();

    $render('home', [
        'pageTitle' => 'Inicio',
        'posts' => $posts,
        'term' => $term,
        'user' => $_SESSION['user'] ?? null,
    ]);
});

/*
 * Registro.
 */
$router->get('/register', function () use ($render) {
    $render('auth/register', [
        'pageTitle' => 'Crear cuenta',
        'error' => null,
        'username' => '',
        'email' => '',
    ]);
});

$router->post('/register', function () use (
    $authController,
    $render,
    $checkCsrf
) {
    $checkCsrf();

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $authController->register(
        $username,
        $email,
        $password
    );

    if ($result['success']) {
        header('Location: /login?registered=1');
        exit;
    }

    $render('auth/register', [
        'pageTitle' => 'Crear cuenta',
        'error' => $result['message'],
        'username' => $username,
        'email' => $email,
    ]);
});

/*
 * Inicio de sesión.
 */
$router->get('/login', function () use ($render) {
    $render('auth/login', [
        'pageTitle' => 'Iniciar sesión',
        'error' => null,
        'email' => '',
        'registered' => isset($_GET['registered']),
    ]);
});

$router->post('/login', function () use (
    $authController,
    $render,
    $checkCsrf
) {
    $checkCsrf();

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $authController->login($email, $password);

    if ($result['success']) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        header('Location: /');
        exit;
    }

    $render('auth/login', [
        'pageTitle' => 'Iniciar sesión',
        'error' => $result['message'],
        'email' => $email,
        'registered' => false,
    ]);
});

/*
 * Cierre de sesión.
 */
$router->post('/logout', function () use (
    $authController,
    $checkCsrf
) {
    $checkCsrf();
    $authController->logout();

    header('Location: /');
    exit;
});

/*
 * Crear publicación.
 */
$router->get('/posts/create', function () use ($render, $requireLogin) {
    $requireLogin();

    $render('posts/create', [
        'pageTitle' => 'Crear publicación',
        'error' => null,
        'title' => '',
        'body' => '',
        'category' => '',
    ]);
});

$router->post('/posts/create', function () use (
    $postController,
    $render,
    $requireLogin,
    $checkCsrf
) {
    $requireLogin();
    $checkCsrf();

    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';
    $category = $_POST['category'] ?? '';

    $result = $postController->create(
        (int) $_SESSION['user']['id'],
        $title,
        $body,
        $category
    );

    if ($result['success']) {
        header('Location: /');
        exit;
    }

    $render('posts/create', [
        'pageTitle' => 'Crear publicación',
        'error' => $result['message'],
        'title' => $title,
        'body' => $body,
        'category' => $category,
    ]);
});

/*
 * Ver una publicación y sus comentarios.
 */
$router->get('/posts/{id}', function (int $id) use (
    $postController,
    $commentController,
    $render
) {
    $post = $postController->show($id);

    if ($post === null) {
        http_response_code(404);
        echo 'La publicación no existe.';
        return;
    }

    $render('posts/show', [
        'pageTitle' => $post['title'],
        'post' => $post,
        'comments' => $commentController->index($id),
        'user' => $_SESSION['user'] ?? null,
        'error' => null,
    ]);
});

/*
 * Crear comentario.
 */
$router->post('/posts/{id}/comments', function (int $id) use (
    $postController,
    $commentController,
    $requireLogin,
    $checkCsrf
) {
    $requireLogin();
    $checkCsrf();

    $result = $commentController->create(
        $id,
        (int) $_SESSION['user']['id'],
        $_POST['body'] ?? ''
    );

    if (!$result['success']) {
        http_response_code(422);
        echo htmlspecialchars(
            $result['message'],
            ENT_QUOTES,
            'UTF-8'
        );
        return;
    }

    header('Location: /posts/' . $id);
    exit;
});

/*
 * Eliminar comentario propio.
 */
$router->post('/comments/{id}/delete', function (int $id) use (
    $commentController,
    $commentModel,
    $requireLogin,
    $checkCsrf
) {
    $requireLogin();
    $checkCsrf();

    $comment = $commentModel->getById($id);

    if ($comment === null) {
        http_response_code(404);
        echo 'El comentario no existe.';
        return;
    }

    $postId = (int) $comment['post_id'];

    $result = $commentController->delete(
        $id,
        (int) $_SESSION['user']['id']
    );

    if (!$result['success']) {
        http_response_code(403);
        echo htmlspecialchars(
            $result['message'],
            ENT_QUOTES,
            'UTF-8'
        );
        return;
    }

    header('Location: /posts/' . $postId);
    exit;
});

/*
 * Formulario de edición de una publicación propia.
 */
$router->get('/posts/{id}/edit', function (int $id) use (
    $postController,
    $render,
    $requireLogin
) {
    $requireLogin();

    $post = $postController->show($id);

    if ($post === null) {
        http_response_code(404);
        echo 'La publicación no existe.';
        return;
    }

    if ((int) $post['user_id'] !== (int) $_SESSION['user']['id']) {
        http_response_code(403);
        echo 'No tienes permiso para editar esta publicación.';
        return;
    }

    $render('posts/edit', [
        'pageTitle' => 'Editar publicación',
        'post' => $post,
        'error' => null,
        'title' => $post['title'],
        'body' => $post['body'],
        'category' => $post['category'],
    ]);
});

/*
 * Procesar edición.
 */
$router->post('/posts/{id}/edit', function (int $id) use (
    $postController,
    $render,
    $requireLogin,
    $checkCsrf
) {
    $requireLogin();
    $checkCsrf();

    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';
    $category = $_POST['category'] ?? '';

    $result = $postController->update(
        $id,
        (int) $_SESSION['user']['id'],
        $title,
        $body,
        $category
    );

    if ($result['success']) {
        header('Location: /posts/' . $id);
        exit;
    }

    $post = $postController->show($id);

    if ($post === null) {
        http_response_code(404);
        echo 'La publicación no existe.';
        return;
    }

    $render('posts/edit', [
        'pageTitle' => 'Editar publicación',
        'post' => $post,
        'error' => $result['message'],
        'title' => $title,
        'body' => $body,
        'category' => $category,
    ]);
});

/*
 * Eliminar publicación propia.
 */
$router->post('/posts/{id}/delete', function (int $id) use (
    $postController,
    $requireLogin,
    $checkCsrf
) {
    $requireLogin();
    $checkCsrf();

    $result = $postController->delete(
        $id,
        (int) $_SESSION['user']['id']
    );

    if (!$result['success']) {
        http_response_code(403);
        echo htmlspecialchars(
            $result['message'],
            ENT_QUOTES,
            'UTF-8'
        );
        return;
    }

    header('Location: /');
    exit;
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
