<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoAddRequest extends RpcRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // get the parent validation array and add the specific parameters to validate
        $rules = parent::rules();
        
        $videoAddRules = [
            'params.filename' => 'required|string|min:1',
            'params.filesize' => 'required|numeric|min:0',
            'params.filetype' => 'required|string|in:video/mp4',
        ];

        return array_merge($rules, $videoAddRules);
    }
}
