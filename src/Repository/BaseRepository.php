<?php

namespace RaviyaTechnical\RepositoryGenerator\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Log Error 

    public function LogError($exception, $dump = true)
    {
        if (config('app.env') == 'local' && $dump) {
            dd($exception);
        }
        Log::error($exception);
    }
    
    public function query()
    {
        return $this->model::query();
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        try {
            return $this->model->with($relations)->get($columns);
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection
    {
        try {
            return $this->model->onlyTrashed()->get();
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Find model by id.
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model {
        try {
            return $this->model->select($columns)->with($relations)->findOrFail($modelId)->append($appends);
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Find trashed model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findTrashedById(int $modelId): ?Model
    {
        try {
            return $this->model->withTrashed()->findOrFail($modelId);
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Find only trashed model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findOnlyTrashedById(int $modelId): ?Model
    {
        try {
            return $this->model->onlyTrashed()->findOrFail($modelId);
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model
    {
        try {   
            $model = $this->model->create($payload);
            return $model->fresh();
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $payload
     * @return bool
     */
    public function update(int $modelId, array $payload): bool
    {
        try {  
            $model = $this->findById($modelId);
            return $model->update($payload);
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        try {  
            return $this->findById($modelId)->delete();
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Restore model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool
    {
        try {  
            return $this->findOnlyTrashedById($modelId)->restore();
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }

    /**
     * Permanently delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool
    {
        try {  
            return $this->findTrashedById($modelId)->forceDelete();
        } catch (\Exception $e) {
            $this->LogError($e);
        }
    }
}
