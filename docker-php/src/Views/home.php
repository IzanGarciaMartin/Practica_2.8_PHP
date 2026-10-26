
<section class="hero">
    <p class="muted">TU ESPACIO PARA COMPARTIR</p>
    <h1>Ideas, preguntas y conocimiento.</h1>
    <p>Encuentra respuestas, comparte lo que sabes y participa en la comunidad.</p>

    <?php if ($user !== null): ?>
        <p>¡Bienvenido, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</p>
        <p><a class="button" href="/posts/create">Crear publicación</a></p>
    <?php else: ?>
        <p><a class="button" href="/register">Únete a la comunidad</a></p>
    <?php endif; ?>
</section>

<section class="card">
    <form method="GET" action="/">
        <label for="q">Buscar publicaciones</label>
        <input
            type="search"
            id="q"
            name="q"
            placeholder="Busca por título, contenido o categoría..."
            value="<?= htmlspecialchars($term, ENT_QUOTES, 'UTF-8') ?>"
        >
        <button class="button" type="submit">Buscar</button>
    </form>
</section>

<div class="section-heading">
    <h2>Últimas publicaciones</h2>
    <span class="muted"><?= count($posts) ?> resultados</span>
</div>

<?php if (empty($posts)): ?>
    <section class="card">
        <h3>No hay publicaciones todavía</h3>
        <p class="muted">Cuando alguien publique algo, aparecerá aquí.</p>
    </section>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="card">
            <p class="category">
                <?= htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <h2>
                <a href="/posts/<?= (int) $post['id'] ?>">
                    <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>

            <p>
                <?= nl2br(htmlspecialchars(
                    mb_strimwidth($post['body'], 0, 240, '...'),
                    ENT_QUOTES,
                    'UTF-8'
                )) ?>
            </p>

            <div class="post-meta">
                <span>
                    Por <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span>
                    <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <a href="/posts/<?= (int) $post['id'] ?>">Ver publicación y comentarios →</a>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
