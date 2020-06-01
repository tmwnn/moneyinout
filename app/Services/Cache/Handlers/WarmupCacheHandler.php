<?php
/**
 * Description of WarmupCacheHandler.php
 * Хендлер для прогрева кэша
 */

namespace App\Services\Cache\Handlers;

use App\Services\Cache\CacheKeyManager;
use App\Services\Cache\Tag;
use App\Services\Categories\Repositories\CachedCategoryRepository;
use App\Services\Categories\Repositories\CachedCategoryRepositoryInterface;
use App\Services\Operations\Repositories\CachedOperationRepository;
use App\Services\Operations\Repositories\CachedOperationRepositoryInterface;
use App\Services\Users\Repositories\UserRepositoryInterface;
use Cache;

class WarmupCacheHandler
{
    const CACHE_SEARCH_SECONDS = 60;
    private $cacheKeyManager;
    private $cachedCategoryRepository;
    private $cachedOperationRepository;
    public function __construct(CacheKeyManager $cacheKeyManager,
                                CachedCategoryRepositoryInterface $cachedCategoryRepository,
                                CachedOperationRepositoryInterface $cachedOperationRepository
    )
    {
        $this->cacheKeyManager = $cacheKeyManager;
        $this->cachedCategoryRepository = $cachedCategoryRepository;
        $this->cachedCategoryRepository = $cachedOperationRepository;
    }

    /**
     * Прогрев кэша CMS
     */
    public function warmUpForCms()
    {
        Cache::tags([Tag::CMS])->flush();
        $this->cachedCategoryRepository->searchByNames('');
    }

    /**
     * Прогрев кэша у пользователей
     */
    public function warmUpForUsers()
    {
        $this->cachedOperationRepository->clearSearchCache();
        $usersIds = $this->cachedOperationRepository->getIncomeUsersIds();
        foreach ($usersIds as $userId) {
            $this->cachedOperationRepository->search('', $userId);
            $this->cachedOperationRepository->sum('', $userId);
        }
    }
}
