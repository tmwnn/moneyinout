<?php
/**
 * Хэндлер для изменения категорий
 */

namespace App\Services\Operations\Handlers;


use App\Models\Category;
use App\Models\Operation;
use App\Services\Operations\Repositories\OperationRepositoryInterface;
use Carbon\Carbon;

class UpdateOperationHandler
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
     * @param array $data
     * @return Operation
     */
    public function handle(int $id, array $data): Operation
    {
        $catName = Category::find($data['category_id'])->name ?? '';
        $data['comment'] = $data['comment'] ?? '';
        $data['search'] = $catName . ': ' . $data['comment'];
        return $this->operationRepository->updateFromArray($id, $data);
    }

}
