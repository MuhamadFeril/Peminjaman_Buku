<?php

namespace App\Repositories;

use App\Repositories\interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function find($id) {
        return $this->model->find($id);
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function update($id, array $data) {
        $record = $this->find($id);
        if ($record && $record->update($data)) {
            return $record->fresh();
        }
        return null;
    }

    public function delete($id) {
        $record = $this->find($id);
        return $record ? $record->delete() : false;
    }
    

}