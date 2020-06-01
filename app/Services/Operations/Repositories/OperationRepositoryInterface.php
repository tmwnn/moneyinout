<?php
/**
 * Интерфейс репозитория для доходов
 */

namespace App\Services\Operations\Repositories;


interface OperationRepositoryInterface
{
    public function search(string $search = '', int $userId = 0);

    public function sum(string $search = '', int $userId = 0);

    public function createFromArray(array $data);

    public function getUsersIds();
    /*
    public function updateFromArray(int $id, array $data);

    public function delete(int $id);
    */
}
