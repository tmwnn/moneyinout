<?php
/**
 * Хэндлер для добавления
 */

namespace App\Services\Operations\Handlers;


use App\Models\Category;
use App\Models\Operation;
use App\Services\Operations\Repositories\OperationRepositoryInterface;
use Carbon\Carbon;

class CreateOperationHandler
{
    /** @var OperationRepositoryInterface  */
    private $operationRepository;

    public function __construct(
        OperationRepositoryInterface $operationRepository
    )
    {
        $this->operationRepository = $operationRepository;
    }

    /**
     * @param array $data
     * @return Operation
     */
    public function handle(array $data): Operation
    {
        //$data['user_id'] = \Auth::user()->id;
        $catName = '';
        if (!empty($data['category_id'])) {
            $catName = Category::find($data['category_id'])->name ?? '';
        }
        $data['comment'] = $data['comment'] ?? '';
        $data['tags'] = $data['tags'] ?? '';
        $date = date('d.m.Y', strtotime($data['date']));
        $summ = round($data['summ']);
        $data['search'] = "{$date} {$summ} {$catName}: {$data['comment']} {$data['tags']}";
        return $this->operationRepository->createFromArray($data);
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->operationRepository->getUsers();
    }
}
