<?php
/**
 * Eloquent репозиторий для доходов
 */

namespace App\Services\Operations\Repositories;

use App\Models\Operation;
use App\Services\Operations\Repositories\OperationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class EloquentOperationRepository implements OperationRepositoryInterface
{

    /**
     * @param array $filters
     * @param int $userId
     * @return Builder
     */
    private function searchByFilters($filters = [], $userId = 0)
    {
        $Operations = Operation::where('user_id', $userId);
        if (!empty($filters)) {
            if (!empty($filters['searchString'])) {
                $Operations->where('search', 'like', '%' . $filters['searchString'] . '%');
            }
            if (strlen($filters['summMin'] ?? '')) {
                $Operations->where('summ', '>=', intval($filters['summMin']));
            }
            if (strlen($filters['summMax'] ?? '')) {
                $Operations->where('summ', '<=', intval($filters['summMax']));
            }
            if (!empty($filters['dateMin'])) {
                $Operations->where('date', '>=', ($filters['dateMin']));
            }
            if (!empty($filters['dateMax'])) {
                $Operations->where('date', '<=', ($filters['dateMax']));
            }
            if (!empty($filters['categories'])) {
                $categoriesIds = collect($filters['categories'])->pluck('code')->toArray();
                $categoriesIds = array_map('intval', $categoriesIds);
                $Operations->whereIn('category_id', $categoriesIds);
            }
        }
        return $Operations;
    }

    /**
     * @param array $filters
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($filters = [], int $userId = 0)
    {
        $Operations = $this->searchByFilters($filters, $userId);
        return $Operations->orderBy('id', 'desc')->paginate();
    }

    /**
     * @param array $filters
     * @param int $userId
     * @return integer
     */
    public function sum($filters = [], int $userId = 0)
    {
        $Operations = $this->searchByFilters($filters, $userId);
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
