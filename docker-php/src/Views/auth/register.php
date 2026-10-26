<section class="card form-card">
    <p class="muted">ÚNETE A LA COMUNIDAD</p>
    <h1>Crear cuenta</h1>
    <p class="muted">Regístrate para compartir tus publicaciones.</p>

    <?php if ($error !== null): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="/register" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <label for="username">Nombre de usuario</label>
        <input
            type="text"
            id="username"
            name="username"
            maxlength="50"
            autocomplete="username"
            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            autocomplete="email"
            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <button class="button" type="submit">Crear cuenta</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
</section>
