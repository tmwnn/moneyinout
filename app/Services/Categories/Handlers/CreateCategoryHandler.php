<?php
/**
 * Хэндлер для добавления категорий
 */

namespace App\Services\Categories\Handlers;


use App\Models\Category;
use App\Services\Categories\Repositories\CategoryRepositoryInterface;
use Carbon\Carbon;

class CreateCategoryHandler
{

    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $data
     * @return Category
     */
    public function handle(array $data): Category
    {
        $data['name'] = trim(ucfirst($data['name'] ?? ''));
        return $this->categoryRepository->createFromArray($data);
    }

}
