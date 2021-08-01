<?php
/**
 * Интерфейс репозитория для доходов
 */

namespace App\Services\Operations\Repositories;


interface OperationRepositoryInterface
{
    public function search(array $filters = [], int $userId = 0, $limit = 10);

    public function sum(array $filters = [], int $userId = 0);

    public function stat(array $filters = [], int $userId = 0, $group = 'm', $type = '');

    public function createFromArray(array $data);

    public function getUsersIds();

    public function updateFromArray(int $id, array $data);

    public function delete(int $id);

}
