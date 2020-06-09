<?php
/**
 * Description of UpdateCategoryRequest.php
 */

namespace App\Http\Controllers\Common\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateOperationRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        $id = $this->request->get('id', 0);
        return [
        ];
    }

}
