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
        /*
        $search = $request->get('search', '');
        $ts1 = microtime(true);
        $userId = \Auth::user()->id ?? 0;
        $operations = $this->operationsService->search($search, $userId);
        $summ = $this->operationsService->sum($search, $userId);
        $ts2 = microtime(true);
        //\Log::channel('info')->debug('Operations/search_and_summ' . ($request->get('no_cache') ? ' (no cache)' : '') . ': '. ($ts2 - $ts1));
        */
        //$userId = \Auth::user()->id ?? 0;
        //$categories = $this->categoriesService->searchByUser($userId);
        //dd($this->operationsService->stat([], 1, 'y'));
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
        $ts1 = microtime(true);
        $userId = \Auth::user()->id ?? 0;
        $operations = $this->operationsService->search($searchArr, $userId);
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
        $ts2 = microtime(true);
        //\Log::channel('info')->debug('Operations/search_and_summ' . ($request->get('no_cache') ? ' (no cache)' : '') . ': '. ($ts2 - $ts1));
        return response()->json($result,200);
    }


    /**
     * Сохранение операции
     *
     * @param Request $request
     * @return string
     */
    public function store(StoreOperationRequest $request): string
    {
        try {
            $data = $request->all();
            $userId = \Auth::user()->id ?? 0;
            $data['user_id'] = $userId;
            $operation = $this->operationsService->store($request->all());

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
        try {
            $operation = $this->operationsService->find($id);
            if ($operation->user_id == $userId) {
                $operation = $this->operationsService->update($id, $request->all());
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
