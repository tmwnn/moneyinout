<?php
/**
 * Description of CacheKeyManager
 */

namespace App\Services\Cache;


use Illuminate\Http\Request;

class CacheKeyManager
{

    /**
     * @param Request $request
     * @return string
     */
    public function getRequestCategoriesKey(Request $request): string
    {
        return $this->getKey(
            Key::CMS_CATEGORIES_PREFIX,
            $this->generateRequestKeySuffix($request)
        );
    }

    /**
     * @param int $category_id
     * @return string
     */
    public function getCategoryKey(int $category_id): string
    {
        return $this->getKey(
            Key::CMS_CATEGORY_PREFIX,
            $category_id
        );
    }


    /**
     * @param array $filters
     * @return string
     */
    public function getSearchCategoriesKey(array $filters): string
    {
        return $this->getKey(
            Key::CMS_CATEGORIES_PREFIX,
            $this->generateParamsKeySuffix(array_merge(
                [
                    'page' => request()->get('page'),
                ], $filters
            ))
        );
    }

    /**
     * @param array $filters
     * @return string
     */
    public function getSearchOperationsKey(array $filters): string
    {
        return $this->getKey(
            Key::OPERATIONS_PREFIX,
            $this->generateParamsKeySuffix(array_merge(
                [
                    'page' => request()->get('page'),
                ], $filters
            ))
        );
    }


    /**
     * @param Request $request
     * @param array|null $keys
     * @return string
     */
    private function generateRequestKeySuffix(Request $request, ?array $keys = null): string
    {
        return $this->generateParamsKeySuffix([
            $request->fullUrl(),
            $request->all($keys),
        ]);
    }

    /**
     * @param array $params
     * @return string
     */
    private function generateParamsKeySuffix(array $params): string
    {
        return md5(implode('|', $params));
    }

    /**
     * @param string $prefix
     * @param string|null $suffix
     * @return string
     */
    private function getKey(string $prefix, ?string $suffix = null): string
    {
        if (!$suffix) {
            return $prefix;
        }
        return sprintf('%s|%s', $prefix, $suffix);
    }

}
