<?php
/**
 * Description of StoreOperationRequest.php
 */

namespace App\Http\Controllers\Common\Dashboard\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreOperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'summ' => 'required|integer',
        ];
    }

}
