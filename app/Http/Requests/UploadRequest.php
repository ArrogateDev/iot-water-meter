<?php

namespace App\Http\Requests;

class UploadRequest extends BaseRequest
{
    public function rules()
    {
        $rules = [
            'file' => 'bail|required|mimes:gif,jpg,jpeg,bmp,png',
            'type' => 'bail|required|in:default',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'file.required' => 'Please select a valid file type',
            'file.mimes' => 'Please select a valid file type',
            'type.required' => 'Please select a valid file type',
            'type.in' => 'Please select a valid file type',
        ];
    }
}
