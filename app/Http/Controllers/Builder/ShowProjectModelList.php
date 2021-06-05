<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectModelList extends Controller
{

    public function __invoke(Project $project) {
        $project->load(['projectModels', 'projectModels.fields']);
        $array = [];

        foreach ($project->projectModels as $projectModel) {
            $array[$projectModel->id] = $projectModel->label;
        }

        return response()->json($array);
    }


}
