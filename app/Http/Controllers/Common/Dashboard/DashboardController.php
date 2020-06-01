<?php

namespace App\Http\Controllers\Common\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\Dashboard\Requests\StoreOperationRequest;

use App\Services\Operations\OperationsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $operationsService;

    /**
     * Create a new controller instance.
     *
     * @param OperationsService $operationsService
     */

    public function __construct(OperationsService $operationsService)
    {
        $this->middleware('auth');
        $this->operationsService = $operationsService;
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $ts1 = microtime(true);
        $userId = \Auth::user()->id ?? 0;
        $operations = $this->operationsService->search($search, $userId);
        $summ = $this->operationsService->sum($search, $userId);
        $ts2 = microtime(true);
        //\Log::channel('info')->debug('Operations/search_and_summ' . ($request->get('no_cache') ? ' (no cache)' : '') . ': '. ($ts2 - $ts1));

        return view('index', [
            'incomes' => $operations,
            'search' => $search,
            'summ' => $summ,
            'page' => 'index',
        ]);
    }

    /**
     * Сохранение
     *
     * @param Request $request
     * @return string
     */
    public function store(StoreOperationRequest $request): string
    {
        try {
            $country = $this->operationsService->store($request->all());

        } catch (\Exception $e) {
            \Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Store error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return response()->json($country,200)->send();
    }

}
