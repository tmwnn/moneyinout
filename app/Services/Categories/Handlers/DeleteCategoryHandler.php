<?php
/**
 * Хэндлер для удаления категорий
 */

namespace App\Services\Categories\Handlers;


use App\Services\Categories\Repositories\CategoryRepositoryInterface;
use Carbon\Carbon;

class DeleteCategoryHandler
{

    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function handle(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

}
