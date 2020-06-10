<?php
/**
 * Eloquent репозиторий для категорий
 */

namespace App\Services\Categories\Repositories;

use App\Models\Category;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{

    public function find(int $id)
    {
        return Category::find($id);
    }

    /**
     * Поиск и выдача резултата по таблице категорий
     * @param string $name фильтр по наименованию категорий
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchByNames(string $name = '')
    {
        if ($name) {
            $categories = Category::where('name', 'like', "%" . $name . "%")
                ->orderBy('id', 'desc')
                ->paginate();
        } else {
            $categories = Category::orderBy('id', 'desc')->paginate();
        }
        $categories->load('users');
        return $categories;
    }

    /**
     * Поиск и выдача резултата по таблице категорий
     * @param string $name фильтр по наименованию категорий
     * @return array
     */
    public function searchByUser(int $userId = null)
    {
        $userCategories = collect([]);
        if ($userId) {
            $userCategories = Category::where('user_id', $userId)->get()->toArray();
        }
        $categories = Category::whereNull('user_id')->get()->toArray();
        $categories = array_merge($categories, $userCategories);
        return $categories;
    }

    /**
     * Создание записи
     * @param array $data
     * @return Category
     */
    public function createFromArray(array $data)
    {
        $category = new Category();
        $category->create($data);
        return $category;
    }

    /**
     * Изменение записи
     * @param int $id
     * @param array $data
     * @return Category
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
        return Category::where('id', $id)->delete();
    }
}
