<?php

namespace App\Repositories\Contracts;

interface ForecastRepository
{
    /**
     * Get all records from storage.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all();

    /**
     * Find a specfic record by its id.
     *
     * @param int $id
     * @return \Illuminate\Support\Collection|static
     */
    public function find($id);

    /**
     * Find a specfic record by city.
     *
     * @param int $id
     * @return \Illuminate\Support\Collection|static
     */
    public function findByCityId($id);


    /**
     * Find a specfic record by city.
     *
     * @param int $id
     * @return App\ForecastItem $item
     */
    public function getFirstForecastItem($id);

    /**
     * Find a specfic record by city.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSortedForecastItems($id);

    /**
     * Create a new record in storage.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Update a existing record in storage.
     *
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, array $data);

    /**
     * Delete a record from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function delete($id);
}