<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoGetRequest extends RpcRequest
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
         
         $videoGetRules = [
             'params.video_id' => 'required|string|exists:videos,video_id',
         ];
 
         return array_merge($rules, $videoGetRules);
     }
}
