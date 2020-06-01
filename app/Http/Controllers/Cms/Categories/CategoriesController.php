<?php
namespace App\Http\Controllers\Cms\Categories;

use App\Services\Categories\CategoriesService;
use App\Services\Users\UsersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Cms\Categories\Requests\StoreCategoryRequest;
use App\Http\Controllers\Cms\Categories\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Cms\Categories\Requests\DeleteCategoryRequest;
use Log;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\User;
/**
 * Class CategoriesController
 * @package App\Http\Controllers\Cms\Categories
 */
class CategoriesController extends Controller
{

    protected $categoriesService;
    protected $usersService;

    public function __construct(
        CategoriesService $categoriesService,
        UsersService $usersService
    )
    {
        $this->categoriesService = $categoriesService;
        $this->usersService = $usersService;
    }

    /**
     * Вывод списка
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->get('name', '');
        $ts1 = microtime(true);
        $data = $this->categoriesService->searchByNames((string)$name);
        $users[''] = '';
        $users = User::all()->pluck('name', 'id');
        $ts2 = microtime(true);
        //\Log::channel('info')->debug('Categories/searchByNames' . ($request->get('no_cache') ? ' (no cache)' : '') . ': '. ($ts2 - $ts1));
        return view('cms.categories', [
            'categories' => $data,
            'name' => $name,
            'users' => $users,
        ]);
    }


    /**
     * Сохранение страны
     *
     * @param Request $request
     * @return string
     */
    public function store(StoreCategoryRequest $request): string
    {
        try {
            $country = $this->categoriesService->store($request->all());
        } catch (\Exception $e) {
            \Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Store error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return response()->json($country,200)->send();
    }

    /**
     * Изменение страны
     *
     * @param Request $request
     * @return string
     */
    public function update(UpdateCategoryRequest $request): string
    {
        $id = (int)$request->get('id', 0);
        try {
            $category = $this->categoriesService->update($id, $request->all());
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
     * Удаление страны
     *
     * @param Request $request
     * @return string
     */
    public function delete(DeleteCategoryRequest $request): string
    {
        $id = $request->get('id');
        try {
            $this->categoriesService->delete($id);
        } catch (\Exception $e) {
            \Log::channel('error')->error(__METHOD__ . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Delete error',
                'errors' => [[ $e->getMessage() ]],
            ], 400)->send();
        }
        return json_encode([]);
    }
}
