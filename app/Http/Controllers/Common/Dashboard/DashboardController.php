<?php

namespace App\Http\Controllers\Common\Dashboard;

use App\Http\Controllers\Cms\Categories\Requests\DeleteCategoryRequest;
use App\Http\Controllers\Cms\Categories\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\Dashboard\Requests\StoreOperationRequest;

use App\Services\Categories\CategoriesService;
use App\Services\Operations\OperationsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $operationsService;
    protected $categoriesService;

    /**
     * Create a new controller instance.
     *
     * @param OperationsService $operationsService
     * @param CategoriesService $categoriesService
     */

    public function __construct(OperationsService $operationsService, CategoriesService $categoriesService)
    {
        $this->middleware('auth');
        $this->operationsService = $operationsService;
        $this->categoriesService = $categoriesService;
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('index');
    }

    /**
     * Get operations
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function load(Request $request)
    {
        $searchArr = $request->get('search', []);
        $userId = \Auth::user()->id ?? 0;
        $limit = $request->get('limit', 10);
        $operations = $this->operationsService->search($searchArr, $userId, $limit);
        $categories = $this->categoriesService->searchByUser($userId);
        $summ = $this->operationsService->sum($searchArr, $userId);

        $result = [
            'operations' => $operations,
            'categories' => $categories,
            'search' => $searchArr,
            'summ' => $summ,
        ];
        $type = $request->get('type' );
        if ($type == 'stat' || $type == 'graph') {
            $group = $request->get('group');
            $result['stat'] = $this->operationsService->stat($searchArr, $userId, $group, $type);
        }
        return response()->json($result,200);
    }


    /**
     * Сохранение операции
     *
     * @param Request $request
     * @return string
     */
    public function store(Request $request): string
    {
        try {
            $data = $request->all();
            if (!empty($data['summ'])) {
                $data['summ'] = str_replace(',','.', $data['summ']);
                $data['summ'] = preg_replace('/[^\d\.\-]/', '', $data['summ']);
            }
            $userId = \Auth::user()->id ?? 0;
            $data['user_id'] = $userId;
            if ($userId) {
                $operation = $this->operationsService->store($data);
            } else {
                throw new \Exception('Access denied!');
            }
        } catch (\Exception $e) {
            //\Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Store error',
                'errors' => [[$e->getMessage()]],
            ], 400)->send();
        }
        return response()->json($operation, 200)->send();
    }

    /**
     * Изменение операции
     *
     * @param Request $request
     * @return string
     */
    public function update(Request $request): string
    {
        $id = (int)$request->get('id', 0);
        $userId = \Auth::user()->id ?? 0;
        $data = $request->all();
        if (!empty($data['summ'])) {
            $data['summ'] = str_replace(',','.', $data['summ']);
            $data['summ'] = preg_replace('/[^\d\.\-]/', '', $data['summ']);
        }
        try {
            $operation = $this->operationsService->find($id);
            if ($operation->user_id == $userId) {
                $operation = $this->operationsService->update($id, $data);
            } else {
                throw new \Exception('Access denied!');
            }
        } catch (\Exception $e) {
            //\Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Update error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode($operation);
    }


    /**
     * Удаление операции
     *
     * @param Request $request
     * @return string
     */
    public function delete(Request $request): string
    {
        $id = $request->get('id');
        $userId = \Auth::user()->id ?? 0;
        try {
            $operation = $this->operationsService->find($id);
            if ($operation->user_id == $userId) {
                $this->operationsService->delete($id);
            } else {
                throw new \Exception('Access denied!');
            }
        } catch (\Exception $e) {
            //\Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Delete error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode([]);
    }

    /**
     * Удаление категории
     *
     * @param Request $request
     * @return string
     */
    public function deleteCategory(Request $request): string
    {
        $id = $request->get('id');
        try {
            $userId = \Auth::user()->id ?? 0;
            $cat = $this->categoriesService->find($id);
            if ($userId && $cat->user_id == $userId) {
                $this->categoriesService->delete($id);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Delete error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode([]);
    }

    /**
     * Сохранение категории
     *
     * @param Request $request
     * @return string
     */
    public function updateCategory(Request $request): string
    {
        $id = $request->get('id');
        try {
            $userId = \Auth::user()->id ?? 0;
            $cat = $this->categoriesService->find($id);
            if ($userId && $cat->user_id == $userId) {
                $this->categoriesService->update($id, ['name' => $request->get('name')]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode([]);
    }

    /**
     * Добавление категории
     *
     * @param Request $request
     * @return string
     */
    public function storeCategory(Request $request): string
    {
        try {
            $userId = \Auth::user()->id ?? 0;
            if ($userId) {
                $this->categoriesService->store(['name' => $request->get('name'), 'user_id' => $userId]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Store error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode([]);
    }
}
