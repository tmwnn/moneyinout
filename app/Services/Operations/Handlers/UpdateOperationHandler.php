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
        $catName = '';
        if (!empty($data['category_id'])) {
            $catName = Category::find($data['category_id'])->name ?? '';
        }
        $data['comment'] = $data['comment'] ?? '';
        $data['tags'] = $data['tags'] ?? '';
        $data['search'] = "{$catName}: {$data['comment']} {$data['tags']}";
        return $this->operationRepository->updateFromArray($id, $data);
    }

}
