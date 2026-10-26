<section class="card form-card">
    <p class="muted">ACTUALIZA TU CONTENIDO</p>
    <h1>Editar publicación</h1>

    <?php if ($error !== null): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="/posts/<?= (int) $post['id'] ?>/edit" method="POST">
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

        <button class="button" type="submit">Guardar cambios</button>
        <a href="/posts/<?= (int) $post['id'] ?>">Cancelar</a>
    </form>
</section>
