<?php

namespace App\Services;

use App\Repo\UserRepositoryInterface; 

class UserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);

        $this->userRepository = $userRepository;
    }

    public function getUserByEmail(string $email)
    {
        return $this->userRepository->getUserByEmail($email);
    }
}
