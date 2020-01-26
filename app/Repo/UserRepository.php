<?php

namespace App\Repo;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUserByToken($token)
    {
        return $this->model
            ->resetPasswordToken($token)
            ->first();
    }

    public function getUserByEmail($email)
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }
    
    public function chainQueryWhere(Builder $query, string $key, string $value)
    {
        return $query->where($key, $value);
    }

    public function chainQueryWhereIn(Builder $query, string $key, array $arrayValue)
    {
        return $query->whereIn($key, $arrayValue);
    }

    public function createQuery()
    {
        return $this->model->query();
    }
}
