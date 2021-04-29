<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:20'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
