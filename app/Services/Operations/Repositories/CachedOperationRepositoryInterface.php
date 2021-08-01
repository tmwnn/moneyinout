<?php
/**
 * Description of CachedOperationRepositoryInterface.php
 */

namespace App\Services\Operations\Repositories;


use App\Models\Operation;

interface CachedOperationRepositoryInterface
{

    public function sum($filters, $userId);

    public function search($filters, $userId, $limit = 10);

    public function clearSearchCache();

    public function getUsersIds();

}
