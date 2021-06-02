<?php

namespace App\Http\Requests\ProjectModel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProjectModel extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return auth()->user()->projects->contains($this->project_id);
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
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'min:3', Rule::unique('project_models')->where(function ($query) {
                return $query->where('project_id', $this->project_id);
            })],
            'label' => ['required', 'string', 'min:1'],
            'soft_delete' => 'sometimes|boolean',
            'project_model_id' => ['nullable', 'exists:project_models,id'],
            'parent_menu_id' => ['nullable', 'exists:parent_menus,id'],
            'fields' => ['nullable', 'array'],
            'fields.*.type' => ['required_with:fields', 'string'],
            'fields.*.is_searchable' => ['nullable', 'boolean'],
            'fields.*.label' => ['required_with:fields', 'string'],
            'fields.*.database_name' => ['required_with:fields', 'string'],
            'fields.*.validations' => ['nullable', 'array'],
            'fields.*.in_view' => ['nullable', 'boolean'],
        ];
    }
}
