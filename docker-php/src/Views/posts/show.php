<section class="card">
    <p class="category">
        <?= htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <h1><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h1>

    <div class="post-meta">
        <span>
            Publicado por
            <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span><?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="post-body">
        <?= nl2br(htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8')) ?>
    </div>

    <?php if (
        $user !== null &&
        (int) $user['id'] === (int) $post['user_id']
    ): ?>
        <div class="post-actions">
            <a class="button" href="/posts/<?= (int) $post['id'] ?>/edit">
                Editar publicación
            </a>

            <form
                action="/posts/<?= (int) $post['id'] ?>/delete"
                method="POST"
                onsubmit="return confirm('¿Seguro que quieres eliminar esta publicación?');"
            >
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button class="button button-danger" type="submit">
                    Eliminar publicación
                </button>
            </form>
        </div>
    <?php endif; ?>

    <p><a href="/">← Volver al tablón</a></p>
</section>

<section class="card">
    <h2>Comentarios (<?= count($comments) ?>)</h2>

    <?php if (empty($comments)): ?>
        <p class="muted">Todavía no hay comentarios. ¡Sé el primero!</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <article class="comment">
                <div class="post-meta">
                    <strong>
                        <?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                    <span>
                        <?= htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>

                <p>
                    <?= nl2br(htmlspecialchars($comment['body'], ENT_QUOTES, 'UTF-8')) ?>
                </p>

                <?php if (
                    $user !== null &&
                    (int) $user['id'] === (int) $comment['user_id']
                ): ?>
                    <form
                        action="/comments/<?= (int) $comment['id'] ?>/delete"
                        method="POST"
                        onsubmit="return confirm('¿Seguro que quieres eliminar este comentario?');"
                    >
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <button class="button button-danger" type="submit">
                            Eliminar comentario
                        </button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($user !== null): ?>
        <h3>Añadir un comentario</h3>

        <form action="/posts/<?= (int) $post['id'] ?>/comments" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <label for="body">Tu comentario</label>
            <textarea
                id="body"
                name="body"
                maxlength="5000"
                required
            ></textarea>

            <button class="button" type="submit">Publicar comentario</button>
        </form>
    <?php else: ?>
        <p><a href="/login">Inicia sesión</a> para dejar un comentario.</p>
    <?php endif; ?>
</section>
