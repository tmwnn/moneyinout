<?php
/**
 * Description of UpdateCategoryRequest.php
 */

namespace App\Http\Controllers\Cms\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        $id = $this->request->get('id', 0);
        return [
            'name' => 'required|unique:countries,name,' . $id . '|max:255',
        ];
    }

}
