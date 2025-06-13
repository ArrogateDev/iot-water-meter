<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class BaseRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }
}
