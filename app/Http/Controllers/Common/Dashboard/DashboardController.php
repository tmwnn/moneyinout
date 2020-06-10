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
        //dd($categories);
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
        $search = $request->get('search', []);
        $ts1 = microtime(true);
        $userId = \Auth::user()->id ?? 0;
        $operations = $this->operationsService->search($search['searchString'] ?? '', $userId);
        $categories = $this->categoriesService->searchByUser($userId);
        $summ = $this->operationsService->sum($search['searchString'] ?? '', $userId);
        $ts2 = microtime(true);
        //\Log::channel('info')->debug('Operations/search_and_summ' . ($request->get('no_cache') ? ' (no cache)' : '') . ': '. ($ts2 - $ts1));

        $result = [
            'operations' => $operations,
            'categories' => $categories,
            'search' => $search,
            'summ' => $summ,
        ];
        return response()->json($result,200)->send();
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
            $country = $this->operationsService->store($request->all());

        } catch (\Exception $e) {
            //\Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Store error',
                'errors' => [[$e->getMessage()]],
            ], 400)->send();
        }
        return response()->json($country, 200)->send();
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
        try {
            $category = $this->operationsService->update($id, $request->all());
        } catch (\Exception $e) {
            //\Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Update error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode($category);
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
        try {
            $this->operationsService->delete($id);
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
}
