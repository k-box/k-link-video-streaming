<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for HTTP-RPC endpoints
 */
class RpcRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|string|unique:tus_uploads_queue,request_id',
                // take into consideration that I might use it in an endpoint where the request id must exists in the database
            'params' => 'required'
        ];
    }
}
