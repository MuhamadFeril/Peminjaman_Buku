<?php

namespace App\Repositories\interfaces;

interface BaseRepositoryInterface
{
    /**
     * Retrieve all records.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * Find a record by its primary key.
     *
     * @param mixed $id
     * @return mixed|null
     */
    public function find($id);

    /**
     * Create a new record.
     *
     * @param array $data
     * @return mixed Newly created model instance
     */
    public function create(array $data);

    /**
     * Update an existing record.
     *
     * @param mixed $id
     * @param array $data
     * @return mixed|null Updated model instance or null on failure
     */
    public function update($id, array $data);

    /**
     * Delete a record.
     *
     * @param mixed $id
     * @return bool True if deleted, false otherwise
     */
    public function delete($id);
}