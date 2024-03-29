<?php
/**
 * Сервис для работы с доходами
 */

namespace App\Services\Operations;

use App\Models\Operation;
use App\Services\Operations\Handlers\CreateOperationHandler;
use App\Services\Operations\Handlers\UpdateOperationHandler;
use App\Services\Operations\Handlers\DeleteOperationHandler;
use App\Services\Operations\Repositories\OperationRepositoryInterface;
use App\Services\Operations\Repositories\CachedOperationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OperationsService
{

    /** @var OperationRepositoryInterface */
    private $repository;
    /** @var CachedOperationRepositoryInterface */
    private $cachedRepository;
    /** @var CreateOperationHandler */
    private $createHandler;
    /** @var UpdateOperationHandler */
    private $updateHandler;
    /** @var DeleteOperationHandler */
    private $deleteHandler;

    public function __construct(
        CreateOperationHandler $createOperationHandler,
        OperationRepositoryInterface $operationRepositoryy,
        CachedOperationRepositoryInterface $cachedOperationRepository,
        UpdateOperationHandler $updateHandler,
        DeleteOperationHandler $deleteHandler
    )
    {
        $this->createHandler = $createOperationHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->repository = $operationRepositoryy;
        $this->cachedRepository = $cachedOperationRepository;
    }


    /**
     * Поиск записи
     * @param integer $id ид
     * @return Operation
     */
    public function find($id): Operation
    {
        return $this->repository->find($id);
    }

    /**
     * Поиск и выдача результата
     * @param array $filters поисковые фильтры
     * @param string $userId ид пользователя
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function search($filters, $userId, $limit = 10): LengthAwarePaginator
    {
        return $this->cachedRepository->search($filters, $userId, $limit);
    }

    /**
     * Сумма дохода
     * @param array $filters поисковая строка
     * @param string $userId ид пользователя
     * @return array
     */
    public function sum($filters, $userId): array
    {
        return $this->cachedRepository->sum($filters, $userId);
    }


    /**
     * Сохранение дохода
     * @param array $data
     * @return Operation
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
     * @return Operation
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


    /**
     * Статистика
     * @param array $filters поисковая строка
     * @param integer $userId ид пользователя
     * @param string $group
     * @param string $type
     * @return array
     */
    public function stat($filters, $userId, $group, $type): array
    {
        return $this->repository->stat($filters, $userId, $group, $type);
    }


}
