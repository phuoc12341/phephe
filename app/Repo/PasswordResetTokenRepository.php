<?php

namespace App\Repo;

use App\Repo\PasswordResetTokenRepositoryInterface;
use App\Models\PasswordResetToken;
use App\Models\User;

class PasswordResetTokenRepository extends BaseRepository implements PasswordResetTokenRepositoryInterface
{
    public function __construct(PasswordResetToken $model)
    {
        parent::__construct($model);
    }
    
    public function deleteByToken(string $token)
    {
        return $this->model->where('token', $token)->delete();
    }

    public function findTokenByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    public function deleteTokenExistingByEmail(string $email)
    {
        return $this->model->where('email', $email)->delete();
    }

    /**
     * Delete a token record by user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function deleteTokenExistingByUser(User $user)
    {
        $this->deleteTokenExistingByEmail($user->email);
    }
}
