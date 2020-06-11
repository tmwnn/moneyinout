<?php
/**
 * Description of CachedOperationRepository.php
 */

namespace App\Services\Operations\Repositories;


use App\Models\Operation;
use App\Services\Cache\CacheKeyManager;
use App\Services\Cache\Tag;
use Cache;

class CachedOperationRepository implements CachedOperationRepositoryInterface
{

    const CACHE_SEARCH_SECONDS = 60;

    /** @var OperationRepositoryInterface */
    private $operationRepository;
    /** @var CacheKeyManager */
    private $cacheKeyManager;

    public function __construct(
        OperationRepositoryInterface $operationRepository,
        CacheKeyManager $cacheKeyManager
    )
    {
        $this->operationRepository = $operationRepository;
        $this->cacheKeyManager = $cacheKeyManager;
    }


    public function search($filters, $userId)
    {
        if (!empty($filters) || request()->get('no_cache')) {
            // для оптимизации кэшируем только без фильтров
            return $this->operationRepository->search($filters, $userId);
        }
        $key = $this->cacheKeyManager->getSearchOperationsKey(['user_id' => $userId]);
        return Cache::tags([Tag::OPERATIONS])
            ->remember($key, self::CACHE_SEARCH_SECONDS, function () use ($userId) {
            return $this->operationRepository->search([], $userId);
        });
    }

    public function sum($filters, $userId): int
    {
        if (!empty($search) || request()->get('no_cache')) {
            // для оптимизации кэшируем только без фильтров
            return $this->operationRepository->sum($filters, $userId);
        }
        $key = $this->cacheKeyManager->getSearchOperationsKey([ 'user_id' => $userId]);
        return Cache::tags([Tag::OPERATIONS_SUM])
            ->remember($key, self::CACHE_SEARCH_SECONDS, function () use ($userId) {
                return $this->operationRepository->sum([], $userId);
            });
    }


    public function clearSearchCache()
    {
        Cache::tags([Tag::OPERATIONS])->flush();
        Cache::tags([Tag::OPERATIONS_SUM])->flush();
    }

    /**
     * Получение ид пользователей для которых есть данные
     * @return array
     */
    public function getUsersIds()
    {
        return $this->operationRepository->getUsersIds();
    }
}
