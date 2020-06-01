<?php
/**
 * Интерфейс репозитория для категорий
 */

namespace App\Services\Categories\Repositories;


interface CategoryRepositoryInterface
{
    public function find(int $id);

    public function searchByNames(string $name = '');

    public function createFromArray(array $data);

    public function updateFromArray(int $id, array $data);

    public function delete(int $id);
}
