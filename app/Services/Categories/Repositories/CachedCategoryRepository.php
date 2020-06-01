<?php
/**
 * Description of CachedCategoryRepository.php
 */

namespace App\Services\Categories\Repositories;


use App\Models\Category;
use App\Services\Cache\CacheKeyManager;
use App\Services\Cache\Tag;
use Cache;

class CachedCategoryRepository implements CachedCategoryRepositoryInterface
{

    const CACHE_SEARCH_SECONDS = 60;

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;
    /** @var CacheKeyManager */
    private $cacheKeyManager;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CacheKeyManager $cacheKeyManager
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function find(int $id)
    {
        $key = $this->cacheKeyManager->getCategoryKey($id);
        return Cache::tags([Tag::CMS, Tag::CATEGORIES])
            ->remember($key, self::CACHE_SEARCH_SECONDS, function () use ($id) {
                return $this->categoryRepository->find($id);
            });
    }

    public function clearCategoryCache(Category $category)
    {
        $key = $this->cacheKeyManager->getCategoryKey($category->id);
        Cache::forget($key);
        $this->clearSearchCache();
    }

    public function searchByNames(string $name)
    {
        if (request()->get('no_cache')) {
            return $this->categoryRepository->searchByNames($name);
        }
        $key = $this->cacheKeyManager->getSearchCategoriesKey(['name' => $name]);
        return Cache::tags([Tag::CMS, Tag::CATEGORIES])
            ->remember($key, self::CACHE_SEARCH_SECONDS, function () use ($name) {
            return $this->categoryRepository->searchByNames($name);
        });
    }

    public function clearSearchCache()
    {
        Cache::tags([Tag::CMS, Tag::CATEGORIES])->flush();
    }

}
