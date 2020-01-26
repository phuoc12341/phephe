<?php

namespace App\Repo;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function getModel();

    /**
     * @param array $columns
     * @param int $offset
     *
     * @return \Illuminate\Support\Collection
     */
    public function fetchList(array $columns = ['*'], int $offset = 0);

    /**
     * @codeCoverageIgnore
     * @param int $page
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateList(int $page = null, array $columns = ['*'], string $orderBy = 'updated_at', string $orderDes = 'desc');

    /**
     * @param array $columns
     *
     * @return boolean
     */
    public function fetchAll(array $columns = ['*']);

    /**
     * @param string $id
     * @param array $columns
     *
     * @return object|null
     */
    public function findById(string $id, array $columns = ['*']);

    /**
     * @param string $id
     * @param array $columns
     *
     * @return object|null
     */
    public function findBySlug(string $slug, array $columns = ['*']);

    /**
     * @param array $ids
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function getListByIds(array $ids, array $columns = ['*'], string $orderBy = 'id', string $orderDes = 'asc');

    /**
     * @param array $data
     *
     * @return bool
     */
    public function insert(array $data);

    /**
     * @param array $columns
     *
     * @return int $id
     */
    public function insertGetId(array $data);

    /**
     * @param array $columns
     *
     * @return Model $model
     */
    public function store(array $data);

    /**
     * @param int $id
     * @param array $data
     *
     * @return int
     */
    public function update(int $id, array $data);

    /**
     * @param int $id
     *
     * @return boolean
     */
    public function deleteById(int $id);

    /**
     * @param array $data
     *
     * @return boolean
     */
    public function deleteMany(array $data);
}
