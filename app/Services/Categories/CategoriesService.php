<?php
/**
 * Сервис для работы со странами
 */

namespace App\Services\Categories;

use App\Models\Category;
use App\Services\Categories\Repositories\CachedCategoryRepositoryInterface;
use App\Services\Categories\Repositories\CategoryRepositoryInterface;
use App\Services\Categories\Handlers\CreateCategoryHandler;
use App\Services\Categories\Handlers\UpdateCategoryHandler;
use App\Services\Categories\Handlers\DeleteCategoryHandler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoriesService
{

    /** @var CategoryRepositoryInterface */
    private $repository;

    /** @var CachedCategoryRepositoryInterface */
    private $cachedRepository;

    /** @var CreateCategoryHandler */
    private $createHandler;
    /** @var UpdateCategoryHandler */
    private $updateHandler;
    /** @var DeleteCategoryHandler */
    private $deleteHandler;
    /** @var array  */
    private $errors = [];

    public function __construct(
        CreateCategoryHandler $createCategoryHandler,
        UpdateCategoryHandler $updateCategoryHandler,
        DeleteCategoryHandler $deleteCategoryHandler,
        CachedCategoryRepositoryInterface $cachedCategoryRepository,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->createHandler = $createCategoryHandler;
        $this->updateHandler = $updateCategoryHandler;
        $this->deleteHandler = $deleteCategoryHandler;
        $this->repository = $categoryRepository;
        $this->cachedRepository = $cachedCategoryRepository;
    }

    /**
     * Поиск и выдача резултата по таблице
     * @param string $name фильтр по наименованию
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchByNames($name): LengthAwarePaginator
    {
        return $this->cachedRepository->searchByNames($name);
    }

    /**
     * Создание
     * @param array $data
     * @return Category
     */
    public function store(array $data)
    {
        $this->cachedRepository->clearSearchCache();
        return $this->createHandler->handle($data);
    }

    /**
     * Изменение
     * @param int $id
     * @param array $data
     * @return Category
     */
    public function update(int $id, array $data)
    {
        $this->cachedRepository->clearSearchCache();
        return $this->updateHandler->handle($id, $data);
    }

    /**
     * Удаление
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $this->cachedRepository->clearSearchCache();
        return $this->deleteHandler->handle($id);
    }

}
