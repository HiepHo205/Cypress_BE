<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Core\Models\Collection as CollectionModel;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->makeModel();
    }

    abstract public function model(): string;

    public function makeModel(): Model
    {
        $model = app($this->model());
        return $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int|string $id, array $attributes): ?Model
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }
        return null;
    }

    public function delete(int|string $id): bool
    {
        $result = $this->find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
    protected function getCollection()
    {
        return CollectionModel::firstOrCreate(
            [
                'api_endpoint' => 'cms',
            ],
            [
                'collection_name' => 'CMS',
                'is_system_type' => true,
            ]
        );
    }
}
