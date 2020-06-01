<?php
/**
 * Description of CachedCategoryRepositoryInterface.php
 */

namespace App\Services\Categories\Repositories;


use App\Models\Category;

interface CachedCategoryRepositoryInterface
{

    public function searchByNames(string $name);

    public function clearSearchCache();

    public function find(int $id);

    public function clearCategoryCache(Category $category);

}
