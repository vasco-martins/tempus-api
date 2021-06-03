<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderMenuRequest;
use App\Jobs\CreateMenuJob;
use App\Models\ProjectModel;
use App\Models\Project;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function reorder(ReorderMenuRequest $request, Project $project) {
        $data = $request->validated();

        foreach($data['ids'] as $order => $id) {
            $projectModel = ProjectModel::find($id);
            $projectModel->update(['order' => $order]);
        }

        $project->update(['deploy_status' => 0]);
        CreateMenuJob::dispatch($project);

        return true;
    }
}
