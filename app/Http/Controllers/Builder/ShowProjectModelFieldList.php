<?php

namespace App\Http\Controllers\Builder;

use App\Helpers\FieldType;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectModelFieldList extends Controller
{
    public function __invoke(Project $project) {
        $project->load(['projectModels', 'projectModels.fields']);
        $array = [];

        foreach ($project->projectModels as $projectModel) {
            $fieldsArray = [];

            foreach($projectModel->fields as $field) {
                if($field->type == FieldType::STRING || $field->type == FieldType::EMAIL || $field->type == FieldType::TEXT) {
                    $fieldsArray[$field->id] = $field->label;
                }
            }

            $array[$projectModel->id] = $fieldsArray;
        }

        return $array;
    }
}
