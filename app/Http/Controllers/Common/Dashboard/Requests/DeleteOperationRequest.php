<?php
/**
 * Description of DeleteCatergoryRequest.php
 */

namespace App\Http\Controllers\Common\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteOperationRequest extends FormRequest
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
