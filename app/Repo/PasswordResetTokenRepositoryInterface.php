<?php

namespace App\Repo;

use App\Models\User;

interface PasswordResetTokenRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByToken(string $token);
    

    public function findTokenByEmail(string $email);


    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    public function deleteTokenExistingByEmail(string $email);

    /**
     * Delete a token record by user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function deleteTokenExistingByUser(User $user);
}
