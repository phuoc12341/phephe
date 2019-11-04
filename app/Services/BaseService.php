<?php

namespace App\Services;

use App\Repo\BaseRepositoryInterface;

abstract class BaseService
{
    protected $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getModel()
    {
        return $this->repository->getModel();
    }

    public function fetchList(array $columns = ['*'], int $offset = 0)
    {
        return $this->repository->fetchList($columns, $offset);
    }

    public function paginateList(int $page = null, array $columns = ['*'], string $orderBy = 'updated_at', string $orderDes = 'desc')
    {
        return $this->repository->paginateList($page, $columns, $orderBy, $orderDes);
    }

    public function fetchAll(array $columns = ['*'])
    {
        return $this->repository->fetchAll($columns);
    }

    public function findById(string $id, array $columns = ['*'])
    {
        return $this->repository->findById($id, $columns);
    }

    public function findBySlug(string $slug, array $columns = ['*'])
    {
        return $this->repository->findBySlug($slug, $columns);
    }

    public function getListByIds(array $ids, array $columns = ['*'], string $orderBy = 'id', string $orderDes = 'asc')
    {
        return $this->repository->getListByIds($ids, $columns, $orderBy, $orderDes);
    }

    public function insert(array $data)
    {
        return $this->repository->insert($data);
    }

    public function insertGetId(array $data)
    {
        return $this->repository->insertGetId($data);
    }

    public function store(array $data)
    {
        return $this->repository->store($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteById(int $id)
    {
        return $this->repository->deleteById($id);
    }

    public function deleteMany(array $data)
    {
        return $this->repository->deleteMany($data);
    }
}
