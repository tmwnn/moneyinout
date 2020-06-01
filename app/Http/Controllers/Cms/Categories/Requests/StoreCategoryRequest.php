<?php
/**
 * Description of StoreCategoryRequest.php
 */

namespace App\Http\Controllers\Cms\Categories\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:countries,name|max:255',
        ];
    }

}
