<?php

declare(strict_types=1);

namespace Ubix\Model;

/**
 * Interface for models that support account logins and AutoLogin
 */
interface AccountInterface
{
    /**
     * Login with a plaintext password
     *
     * @param string $password The plaintext password
     *
     * @return bool Whether or not the login was successful
     */
    public function login(string $password): bool;

    /**
     * Determine if the AutoLogin should proceed
     *
     * @return bool Whether or not the AutoLogin should proceed
     */
    public function autoLogin(): bool;
}
