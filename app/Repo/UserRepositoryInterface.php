<?php

namespace App\Repo;

use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserByToken($token);

    public function getUserByEmail($email);

    public function chainQueryWhere(Builder $query, string $key, string $value);

    public function chainQueryWhereIn(Builder $query, string $key, array $arrayValue);

    public function createQuery();
}
