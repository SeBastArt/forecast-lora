<?php

namespace App\Repositories\Eloquent;

abstract class AbstractEloquentRepository
{
    /**
     * The model that is used by its repository.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Get all records from storage.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->model->get();
    }

    /**
     * Find a specfic record by its id.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record in storage.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a existing record in storage.
     *
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, array $data)
    {
        return $this->model->findOrFail($id)->fill($data)->save();
    }

    /**
     * Delete a record from storage.
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}