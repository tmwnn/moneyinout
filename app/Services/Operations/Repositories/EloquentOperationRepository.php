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
            if (strlen($filters['type'] ?? '')) {
                $Operations->where('type', intval($filters['type']));
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
        return $Operations->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate();
    }

    /**
     * @param array $filters
     * @param int $userId
     * @return array
     */
    public function sum($filters = [], int $userId = 0)
    {
        $Operations = $this->searchByFilters($filters, $userId);
        $total = $Operations->sum('summ');
        $income = $Operations->where('summ', '>', 0)->sum('summ');
        $Operations = $this->searchByFilters($filters, $userId);
        $outcome = $Operations->where('summ', '<', 0)->sum('summ');
        return [
            'total' => $total,
            'income' => $income,
            'outcome' => $outcome,
        ];
    }

    /**
     * @param array $filters
     * @param int $userId
     * @param string $group
     * @param string $type
     * @return array
     */
    public function stat(array $filters = [], int $userId = 0, $group = 'm', $type = '')
    {
        $OperationsList = $this->searchByFilters($filters, $userId)->get();
        $OperationsList->flatMap(function ($values) use ($group) {
            $groupStr = $values->date;
            if ($group == 'm') {
                $groupStr =  substr($values->date, 0, 7);
            }
            if ($group == 'y') {
                $groupStr =  substr($values->date, 0, 4);
            }
            $values->group = $groupStr;
            return $values;
        });
        $groupedList = $OperationsList->groupBy('group');
        $resultArr = [];
        foreach ($groupedList as $group => $items) {
            $total = $items->sum('summ');
            $income = $items->where('summ', '>', 0)->sum('summ');
            $outcome = $items->where('summ', '<', 0)->sum('summ');
            $date = $group;
            if (strlen($group) == 7) {
                $date = $group .'-01';
            }
            if (strlen($group) == 4) {
                $date = $group .'-01-01';
            }
            $resultItem = [
                'group' => $group,
                'total' => $total,
                'income' => $income,
                'outcome' => $outcome * -1,
                'date' => strtotime($date) * 1000,
            ];
            $resultArr[] = $resultItem;
        }
        if ($type == 'graph') {
            $resultArr = array_values(collect($resultArr)->sortBy('group')->toArray());
        } else {
            $resultArr = array_values(collect($resultArr)->sortByDesc('group')->toArray());
        }
        return $resultArr;
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
