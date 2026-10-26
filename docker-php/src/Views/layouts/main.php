<?php
$pageTitle = $pageTitle ?? 'Tablón';
$content = $content ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Tablón</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="navbar">
        <a class="brand" href="/">Tablón<span>.</span></a>

        <nav>
            <a href="/">Inicio</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="/posts/create">Crear publicación</a>

                <span>
                    Hola,
                    <?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?>
                </span>

                <form action="/logout" method="POST">
                    <input
                        type="hidden"
                        name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <button class="button" type="submit">
                        Cerrar sesión
                    </button>
                </form>
            <?php else: ?>
                <a href="/login">Iniciar sesión</a>
                <a class="nav-button" href="/register">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer>
        Tablón · Comparte ideas, preguntas y conocimientos.
    </footer>
</body>
</html>
