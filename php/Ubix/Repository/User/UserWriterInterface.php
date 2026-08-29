<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use Exception;
use Ubix\Model\User;

/**
 * Interface UserWriterInterface
 *
 * Provides methods to write user related data.
 */
interface UserWriterInterface
{
    /**
     * Create a new user
     *
     * @param User $user The user to create
     *
     * @return void The created ID is set on the passed model
     *
     * @throws Exception If the user creation fails
     */
    public function createUser(User $user): void;

    /**
     * Update an existing user
     *
     * @param User $user The user to update
     *
     * @return void
     *
     * @throws Exception If the user update fails
     */
    public function updateUser(User $user): void;
}
