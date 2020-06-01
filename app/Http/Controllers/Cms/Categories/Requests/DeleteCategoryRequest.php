<?php
/**
 * Description of DeleteCatergoryRequest.php
 */

namespace App\Http\Controllers\Cms\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteCategoryRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|numeric',
        ];
    }

}
