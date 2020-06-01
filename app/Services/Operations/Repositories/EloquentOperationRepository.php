<?php
/**
 * Eloquent репозиторий для доходов
 */

namespace App\Services\Operations\Repositories;

use App\Models\Operation;
use App\Services\Operations\Repositories\OperationRepositoryInterface;

class EloquentOperationRepository implements OperationRepositoryInterface
{


    /**
     * @param string $search
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($search = '', int $userId = 0)
    {
        $Operations = Operation::where('user_id', $userId);
        if ($search) {
            $Operations->where('comment', 'like', '%' . $search . '%');
        }
        return $Operations->orderBy('id', 'desc')->paginate();
    }

    /**
     * @param string $search
     * @param int $userId
     * @return integer
     */
    public function sum($search = '', int $userId = 0)
    {
        $Operations = Operation::where('user_id', $userId);
        if ($search) {
            $Operations->where('comment', 'like', '%' . $search . '%');
        }
        return $Operations->sum('summ');
    }

    /**
     * @param array $data
     * @return Operation
     */
    public function createFromArray(array $data)
    {
        $operation = new Operation();
        $operation->create($data);
        return $operation;
    }

    /**
     * @return array
     */
    public function getUsersIds(): array
    {
        $users = Operation::groupBy('user_id')->pluck('user_id')->toArray();
        return $users;
    }

}
