<?php
/**
 * Хэндлер для изменения категорий
 */

namespace App\Services\Categories\Handlers;


use App\Models\Category;
use App\Services\Categories\Repositories\CategoryRepositoryInterface;
use Carbon\Carbon;

class UpdateCategoryHandler
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
     * @param array $data
     * @return Category
     */
    public function handle(int $id, array $data): Category
    {
        $data['name'] = trim(ucfirst($data['name'] ?? ''));
        return $this->categoryRepository->updateFromArray($id, $data);
    }

}
