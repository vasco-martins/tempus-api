<?php

namespace App\Http\Requests\ParentMenu;

use Illuminate\Foundation\Http\FormRequest;

class CreateParentMenuRequest extends FormRequest
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
            'label' => 'required|min:2|max:20',
            'project_id' => 'required|exists:projects,id'
        ];
    }
}
