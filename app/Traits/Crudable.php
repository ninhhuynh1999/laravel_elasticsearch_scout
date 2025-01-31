<?php

namespace App\Traits;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Exceptions\Repository\RepositoryException;

/**
 * Trait Crudable
 * @mixin
 */
trait Crudable
{
    /**
     * Create model with data
     *
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        /** @var Model $model */
        $model = resolve($this->getModelClass());

        if (!$model->fill($data)->save()) {
            return null;
        }

        if (!is_array($model->getKey())) {
            return $model->refresh();
        }

        return $model;
    }

    /**
     * Insert records
     *
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return $this->getQuery()->insert($data);
    }

    /**
     * Update model
     *
     * @param Model|mixed $keyOrModel
     * @param array $data
     * @return Model|null
     * @throws RepositoryException
     */
    public function update($keyOrModel, array $data): ?Model
    {
        $model = $this->resolveModel($keyOrModel);

        if (!$model->update($data)) {
            return null;
        }

        if (!is_array($model->getKey())) {
            return $model->refresh();
        }

        return $model;
    }

    /**
     * Update or create model
     *
     * @param array $attributes
     * @param array $data
     * @return Model|null
     */
    public function updateOrCreate(array $attributes, array $data): ?Model
    {
        return $this->getQuery()->updateOrCreate($attributes, $data);
    }

    /**
     * Delete model
     *
     * @param Model|mixed $keyOrModel
     * @return bool
     * @throws Exception
     */
    public function delete($keyOrModel): bool
    {
        $model = $this->resolveModel($keyOrModel);

        if ($this->isInstanceOfSoftDeletes($model)) {
            return !is_null($model->forceDelete());
        }

        return !is_null($model->delete());
    }
}
