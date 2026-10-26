<?php

namespace App\Models;

class User extends BaseModel
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function getByEmail(string $email): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );
    }

    public function createUser(
        string $username,
        string $email,
        string $password
    ): int {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        return $this->insert([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);
    }

    public function verifyCredentials(
        string $email,
        string $password
    ): ?array {
        $user = $this->getByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);

        return $user;
    }
}
