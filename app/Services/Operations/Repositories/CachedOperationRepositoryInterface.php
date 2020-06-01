<?php
/**
 * Description of CachedOperationRepositoryInterface.php
 */

namespace App\Services\Operations\Repositories;


use App\Models\Operation;

interface CachedOperationRepositoryInterface
{

    public function sum($search, $userId);

    public function search($search, $userId);

    public function clearSearchCache();

    public function getUsersIds();

}
