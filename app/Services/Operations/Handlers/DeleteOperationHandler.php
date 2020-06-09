<?php
/**
 * Хэндлер для удаления
 */

namespace App\Services\Operations\Handlers;


use App\Services\Operations\Repositories\OperationRepositoryInterface;
use Carbon\Carbon;

class DeleteOperationHandler
{

    private $operationRepository;

    public function __construct(
        OperationRepositoryInterface $operationRepository
    )
    {
        $this->operationRepository = $operationRepository;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function handle(int $id): bool
    {
        return $this->operationRepository->delete($id);
    }

}
