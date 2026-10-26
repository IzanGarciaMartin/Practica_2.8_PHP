<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(
        string $username,
        string $email,
        string $password
    ): array {
        $username = trim($username);
        $email = trim($email);

        if (
            $username === '' ||
            $email === '' ||
            $password === ''
        ) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios.',
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El correo electrónico no es válido.',
            ];
        }

        if (strlen($username) > 50) {
            return [
                'success' => false,
                'message' => 'El nombre de usuario es demasiado largo.',
            ];
        }

        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres.',
            ];
        }

        if ($this->userModel->getByEmail($email) !== null) {
            return [
                'success' => false,
                'message' => 'Ese correo electrónico ya está registrado.',
            ];
        }

        try {
            $this->userModel->createUser(
                $username,
                $email,
                $password
            );

            return [
                'success' => true,
                'message' => 'Usuario registrado correctamente.',
            ];
        } catch (\PDOException $e) {
            error_log($e->getMessage());

            return [
                'success' => false,
                'message' => 'No se ha podido registrar el usuario.',
            ];
        }
    }

    public function login(
        string $email,
        string $password
    ): array {
        if (
            trim($email) === '' ||
            $password === ''
        ) {
            return [
                'success' => false,
                'message' => 'Introduce el correo y la contraseña.',
            ];
        }

        $user = $this->userModel->verifyCredentials(
            trim($email),
            $password
        );

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Correo o contraseña incorrectos.',
            ];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user'] = $user;

        return [
            'success' => true,
            'message' => 'Sesión iniciada correctamente.',
        ];
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        session_destroy();
    }
}
