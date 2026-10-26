<section class="card form-card">
    <p class="muted">QUÉ BUENO TENERTE DE VUELTA</p>
    <h1>Iniciar sesión</h1>
    <p class="muted">Accede a tu cuenta para continuar.</p>

    <?php if ($registered): ?>
        <p class="success">
            Cuenta creada correctamente. Ya puedes iniciar sesión.
        </p>
    <?php endif; ?>

    <?php if ($error !== null): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="/login" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

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
            autocomplete="current-password"
            required
        >

        <button class="button" type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="/register">Regístrate</a></p>
</section>
