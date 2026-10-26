<section class="card form-card">
    <p class="muted">COMPARTE CON LA COMUNIDAD</p>
    <h1>Crear publicación</h1>
    <p class="muted">
        Comparte una idea, plantea una pregunta o ayuda a otros usuarios.
    </p>

    <?php if ($error !== null): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="/posts/create" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <label for="title">Título</label>
        <input
            type="text"
            id="title"
            name="title"
            maxlength="150"
            value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="category">Categoría</label>
        <input
            type="text"
            id="category"
            name="category"
            maxlength="100"
            value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="body">Contenido</label>
        <textarea id="body" name="body" required><?= htmlspecialchars($body, ENT_QUOTES, 'UTF-8') ?></textarea>

        <button class="button" type="submit">Publicar</button>
    </form>

    <p><a href="/">Volver al inicio</a></p>
</section>
