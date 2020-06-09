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
            $Operations->where('search', 'like', '%' . $search . '%');
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
            $Operations->where('search', 'like', '%' . $search . '%');
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

    /**
     * Изменение записи
     * @param int $id
     * @param array $data
     * @return Operation
     */
    public function updateFromArray($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    /**
     * Удаление записи
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        return Operation::where('id', $id)->delete();
    }

    public function find(int $id)
    {
        return Operation::find($id);
    }
}
